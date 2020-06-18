<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Customer;
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
     * Display a listing of the order resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $size = $request->get('size', 15);
        $sort = $request->get('sort', 'id');
        $descend = (boolean)$request->get('descend', false);
        $query = Order::query();

        $user = $request->user();
        // 判断用户类型，如果是客户，只能查看自己的订单信息
        if ($user instanceof Customer) {
            $query->select(['id', 'total', 'discount', 'actual', 'date', 'status', 'remark'])
                ->where('customer_id', $user->id)
                ->where('type', '销售');
        }

        $query->when($search = $request->get('search'), function (Builder $query, $search) {
            return $query->where('remark', 'like', "%$search%");
        });

        $query->when($type = $request->get('type'), function (Builder $query, $type) {
            return $query->where('type', $type);
        });

        $query->when($date = $request->get('date'), function (Builder $query, $date) {
            return $query->whereDate('date', $date);
        });

        if ($descend) {
            $query->orderByDesc($sort);
        } else{
            $query->orderBy($sort);
        }

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
        $user = $request->user();
        if ($user instanceof Customer) {
            $attributes['type'] = '销售';
            $attributes['total'] = 0;
            $attributes['date'] = date('Y-m-d');
            $attributes['discount'] = $user->discount;
            $attributes['status'] = 'pending';
            $attributes['remark'] = $request->get('remark');
            $attributes['customer_id'] = $user->id;
        } else {
            $attributes = $this->validate($request, [
                'type' => 'required|in:采购,销售,邮费,满减,损耗',
                'total' => 'required|money',
                'discount' => 'required|integer',
                'date' => 'required|date',
                'remark' => 'nullable|string',
                'customer_id' => 'nullable|uuid'
            ]);
        }

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
        $user = $request->user();
        $scene = $request->get('scene', 'detail');
        switch ($scene) {
            case 'excel':
                $name = sprintf('%s.xlsx', $order->date);
                return (new PurchasingListExport($order->id))->download($name, Excel::XLSX);
            case 'detail':
            default:
                if ($user instanceof Customer) {
                    $order->setHidden([
                        'type', 'cost', 'profit'
                    ]);
                } else {
                    $order->customer = $order->customer()->first();
                }
                $order->tradings = $order->tradings()->get();
                $order->logistic = $order->logistic()->first();
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
     * Modify order status
     *
     * Date: 2020/5/18
     * @param Order $order
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function status(Order $order, Request $request)
    {
        $attributes = $request->validate([
            'status' => 'required|in:pending,confirmed,completed'
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
