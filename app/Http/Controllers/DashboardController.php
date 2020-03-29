<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
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

        $start = strtotime('-30 day');

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
        $days = [];

        while (count($days) <= 30) {
            $start += 86400;
            $days[] = date('Y-m-d', $start);
        }

        $dailyIncome = Order::selectRaw('date, sum(actual) as actual')->where('type', '销售')->groupBy('date')->limit(30)->get();
        $dailyExpend = Order::selectRaw('date, sum(actual) as actual')->whereIn('type', ['采购', '邮费'])->groupBy('date')->limit(30)->get();

        foreach ($days as $day) {
            $in = 0;
            $ex = 0;
            foreach ($dailyIncome as &$income) {
                if ($day == $income->date) {
                    $in = $income->actual;
                    unset($income);
                }
            }

            $summary['tendency'][] = [
                'date' => $day,
                'type' => '收入',
                'actual' => $in,
            ];

            foreach ($dailyExpend as &$expend) {
                if ($day == $expend->date) {
                    $ex = $expend->actual;
                    unset($expend);
                }
            }

            $summary['tendency'][] = [
                'date' => $day,
                'type' => '支出',
                'actual' => $ex,
            ];
        }


        $summary['gross_profit'] = $summary['sales_amount'] + $summary['inventory_cost'] - $summary['purchasing_cost'];
        return success($summary);
    }
}
