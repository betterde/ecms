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

        $summary = [
            'day' => Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->count(),
            'month' => Order::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])->where('type', '销售')->count(),
            'daily_turnover' => (float)Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->sum('total') - $reduced,
            'daily_profit' => (float)Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->sum('profit') - $reduced,
            'inventory_cost' => $inventory_cost,
            'purchasing_cost' => (float)Order::whereIn('type', ['采购', '邮费'])->sum('actual'),
            'sales_amount' => (float)Order::where('type', '销售')->sum('actual'),
            'gross_profit' => 0
        ];

        $summary['gross_profit'] = $summary['sales_amount'] + $summary['inventory_cost'] - $summary['purchasing_cost'];
        return success($summary);
    }
}
