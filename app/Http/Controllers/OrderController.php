<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\JsonResponse;
use App\Exports\PurchasingListExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Order logic controller
 *
 * Date: 2020/2/5
 * @author George
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $size = $request->get('size', 15);
        $query = Order::query();
        $query->when($type = $request->get('type'), function (Builder $query, $type) {
            /**
             * @var Builder $query
             */
            return $query->where('type', $type);
        });

        $query->when($date = $request->get('date'), function (Builder $query, $date) {
            return $query->whereDate('date', $date);
        });

        $query->orderByDesc('id');

        return success($query->paginate($size));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $attributes = $this->validate($request, [
            'type' => 'required|in:采购,销售,邮费,满减,损耗',
            'total' => 'required|money',
            'discount' => 'required|integer',
            'date' => 'required|date',
            'remark' => 'nullable|string',
            'customer_id' => 'nullable|uuid'
        ]);

        if ($attributes['type'] === '邮费') {
            $attributes['actual'] = $attributes['total'];
            $attributes['cost'] = $attributes['total'];
        }

        if ($attributes['type'] === '满减') {
            $attributes['actual'] = $attributes['total'];
        }

        $order = Order::create($attributes);
        return stored($order);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @param Request $request
     * @return JsonResponse|BinaryFileResponse
     */
    public function show(Order $order, Request $request)
    {
        $scene = $request->get('scene', 'detail');
        switch ($scene) {
            case 'excel':
                $name = sprintf('%s.xlsx', $order->date);
                return (new PurchasingListExport($order->id))->download($name, Excel::XLSX);
            case 'detail':
            default:
                if ($order->customer_id) {
                    $order->customer = $order->customer()->first();
                } else {
                    $order->customer = null;
                }
            $order->tradings = $order->tradings()->get();
            return success($order);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Order $order)
    {
        $attributes = $this->validate($request, [
            'type' => 'required|in:采购,销售',
            'total' => 'required|money',
            'discount' => 'required|integer',
            'date' => 'required|date',
            'remark' => 'nullable|string',
        ]);

        $order->update($attributes);
        return updated($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Order $order)
    {
        if ($order->tradings()->count() > 0) {
            return failed('请先删除订单里的商品', 422);
        }
        $order->delete();
        return deleted();
    }
}
