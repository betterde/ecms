<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

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
            'type' => 'required|in:采购,销售',
            'total' => 'required|money',
            'discount' => 'required|integer',
            'date' => 'required|date',
            'remark' => 'nullable|string',
        ]);

        $order = Order::create($attributes);
        return stored($order);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function show(Order $order)
    {
        $order->tradings = $order->tradings()->get();
        return success($order);
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
        $order->delete();
        return deleted();
    }
}
