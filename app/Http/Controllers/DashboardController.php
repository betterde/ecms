<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $summary = [
            'day' => Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->count(),
            'month' => Order::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])->where('type', '销售')->count(),
            'turnover' => (float)Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->sum('total'),
            'profit' => (float)Order::whereDate('date', date('Y-m-d'))->where('type', '销售')->sum('profit')
        ];
        return success($summary);
    }
}
