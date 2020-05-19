<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

/**
 * Commodity discount logic controller
 *
 * Date: 2020/5/19
 * @author George
 * @package App\Http\Controllers
 */
class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = Discount::query();

        $query->when($commodity_id = $request->get('commodity_id'), function (Builder $query, $commodity_id) {
            return $query->where('commodity_id', $commodity_id);
        });

        $discounts = $query->get();
        return success($discounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'commodity_id' => 'required|integer',
            'number' => 'required|integer',
            'price' => 'nullable|money',
            'remark' => 'nullable|string'
        ]);

        $discount = Discount::create($attributes);
        return stored($discount);
    }

    /**
     * Display the specified resource.
     *
     * @param Discount $discount
     * @return JsonResponse
     */
    public function show(Discount $discount)
    {
        return success($discount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Discount $discount
     * @return JsonResponse
     */
    public function update(Request $request, Discount $discount)
    {
        $attributes = $request->validate([
            'number' => 'required|integer',
            'price' => 'nullable|money',
            'remark' => 'nullable|string'
        ]);

        $discount->update($attributes);
        return updated($discount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Discount $discount
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();
        return deleted();
    }
}
