<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Carbon\CarbonPeriod;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardController extends Controller
{
    /**
     * Date: 2020/5/19
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function index(Request $request)
    {
        $start = strtotime('-30 day');
        $startDate = date('Y-m-d 00:00:00', $start);
        $endDate = date('Y-m-d 23:59:59');
        $period = new CarbonPeriod(date('Y-m-d', $start), '1 day', date('Y-m-d'));

        $user = $request->user();

        if ($user instanceof Customer) {
            $summary = [
                'total' => 0,
                'pending' => 0,
                'confirmed' => 0,
                'completed' => 0,
                'expense' => [],
                'quantity' => []
            ];
            $orders = Order::select(['id', 'total', 'discount', 'actual', 'date', 'status', 'remark'])
                ->where('customer_id', $user->id)->get();
            $summary['total'] = $orders->count();
            foreach ($orders as $order) {
                switch ($order->status) {
                    case 'pending':
                        $summary['pending'] += 1;
                        break;
                    case 'confirmed':
                        $summary['confirmed'] += 1;
                        break;
                    case 'completed':
                        $summary['completed'] += 1;
                        break;
                }
            }

            $dailyExpend = Order::selectRaw('date, sum(actual) as actual')
                ->where('customer_id', $user->id)
                ->where('type', '销售')
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('date')->get();

            $dailyOrders = Order::selectRaw('date, count(id) as quantity')
                ->where('customer_id', $user->id)
                ->where('type', '销售')
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('date')->get();

            foreach ($period as $carbon) {
                $actual = 0;
                $quantity = 0;
                $day = $carbon->format('Y-m-d');

                foreach ($dailyExpend as $order) {
                    if ($day == $order->date) {
                        $actual = $order->actual;
                        unset($order);
                    }
                }

                $summary['expense'][] = [
                    'date' => substr($day, 5),
                    'actual' => floatval($actual)
                ];

                foreach ($dailyOrders as $order) {
                    if ($day == $order->date) {
                        $quantity = $order->quantity;
                        unset($order);
                    }
                }

                $summary['quantity'][] = [
                    'date' => substr($day, 5),
                    'quantity' => $quantity
                ];
            }

            return success($summary);
        }

        $inventory_cost = 0;
        $commodities = Commodity::with(['pricings' => function(HasMany $query) {
            $query->where('amount', '>', 0);
        }])->where('amount', '>', 0)->get();

        foreach ($commodities as $commodity) {
            foreach ($commodity->pricings as $pricing) {
                $inventory_cost += $pricing->amount * $pricing->buying;
            }
        }

        $reduced = (float)Order::whereDate('date', date('Y-m-d'))->where('type', '满减')->sum('total');

        $summary = [
            'day' => Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->count(),
            'month' => Order::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])->where('type', '销售')->count(),
            'daily_turnover' => (float)Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->sum('total') - $reduced,
            'daily_profit' => (float)Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->sum('profit') - $reduced,
            'inventory_cost' => $inventory_cost,
            'purchasing_cost' => (float)Order::whereIn('type', ['采购', '邮费'])->sum('actual'),
            'sales_amount' => (float)Order::where('type', '销售')->sum('actual'),
            'gross_profit' => 0,
            'balance' => [],
            'tendency' => [],
        ];

        $dailyIncome = Order::selectRaw('date, sum(actual) as actual, sum(profit) as profit')
            ->where('type', '销售')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')->get();

        $dailyExpend = Order::selectRaw('date, sum(actual) as actual')
            ->whereIn('type', ['采购', '邮费'])
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')->get();

        $dailyOrders = Order::selectRaw('date, count(id) as quantity')
            ->where('type', '销售')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')->get();

        foreach ($period as $carbon) {
            $in = 0;
            $ex = 0;
            $profit = 0;
            $quantity = 0;
            $day = $carbon->format('Y-m-d');

            foreach ($dailyIncome as $income) {
                if ($day == $income->date) {
                    $in = $income->actual;
                    $profit = $income->profit;
                    unset($income);
                }
            }

            $summary['tendency'][] = [
                'date' => substr($day, 5),
                'type' => '收入',
                'actual' => (float)$in,
            ];

            $summary['tendency'][] = [
                'date' => substr($day, 5),
                'type' => '利润',
                'actual' => (float)$profit,
            ];

            foreach ($dailyExpend as $expend) {
                if ($day == $expend->date) {
                    $ex = $expend->actual;
                    unset($expend);
                }
            }

            $summary['tendency'][] = [
                'date' => substr($day, 5),
                'type' => '支出',
                'actual' => (float)$ex,
            ];

            foreach ($dailyOrders as $order) {
                if ($day == $order->date) {
                    $quantity = $order->quantity;
                    unset($order);
                }
            }

            $summary['order_quantity'][] = [
                'date' => substr($day, 5),
                'quantity' => $quantity
            ];
        }

        $summary['gross_profit'] = $summary['sales_amount'] + $summary['inventory_cost'] - $summary['purchasing_cost'];
        return success($summary);
    }
}
