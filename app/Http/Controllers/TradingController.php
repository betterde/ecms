<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use App\Models\Trading;
use App\Models\Pricing;
use App\Models\Commodity;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

/**
 * 交易数据模型
 *
 * Date: 2019/12/28
 * @author George
 * @package App\Http\Controllers
 */
class TradingController extends Controller
{
    /**
     * Date: 2019/12/28
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function index(Request $request)
    {
        $size = $request->get('size', 15);
        $sort = $request->get('sort', 'id');
        $descend = (boolean)$request->post('descend', false);
        $query = Trading::leftJoin('commodities', 'tradings.commodity_id', '=', 'commodities.id');
        $query->select([
            'tradings.id', 'tradings.amount', 'tradings.price', 'tradings.total', 'tradings.commodity_id',
            'commodities.name', 'commodities.brand', 'commodities.unit', 'commodities.specification',
            'tradings.created_at', 'tradings.updated_at'
        ]);

        $query->when($type = $request->get('type'), function (Builder $query, $type) {
            /**
             * @var Builder $query
             */
            return $query->where('type', $type);
        });

        $query->when($order = $request->get('order'), function (Builder $query, $order) {
            return $query->where('order_id', $order);
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
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $attributes = $this->validate($request, [
            'order_id' => 'required|integer',
            'commodity_id' => 'required|integer',
            'amount' => 'required|integer',
            'price' => 'required|money',
            'total' => 'required|money',
        ]);

        try {
            DB::beginTransaction();
            $trading = new Trading($attributes);
            $trading->order->total += $attributes['total'];
            $trading->saveOrFail();
            if ($trading->order->type == '采购') {
                Pricing::create([
                    'trading_id' => $trading->id,
                    'commodity_id' => $trading->commodity_id,
                    'date' => date('Y-m-d'),
                    'amount' => $attributes['amount'],
                    'buying' => $attributes['price']
                ]);
                $trading->commodity->amount += $attributes['amount'];
                $trading->order->cost += $attributes['amount'] * $attributes['price'];
            } elseif ($trading->order->type == '销售') {
                $commodity = Commodity::find($attributes['commodity_id']);
                if ($commodity->amount < $attributes['amount']) {
                    return failed(sprintf('商品 %s 的库存仅剩 %d', $commodity->name, $commodity->amount), 422);
                }

                /**
                 * @var Pricing[] $pricings
                 */
                $pricings = Pricing::where('commodity_id', $attributes['commodity_id'])->where('amount', '>', 0)->get();
                $amount = $attributes['amount'];
                foreach ($pricings as $pricing) {
                    if ($pricing->amount >= $amount) {
                        $pricing->amount -= $amount;
                        $pricing->saveOrFail();
                        $trading->order->cost += $amount * $pricing->buying;
                        Inventory::create([
                            'trading_id' => $trading->id,
                            'pricing_id' => $pricing->id,
                            'amount' => $amount,
                        ]);
                        break;
                    } else {
                        $amount -= $pricing->amount;
                        $trading->order->cost += $pricing->amount * $pricing->buying;
                        Inventory::create([
                            'trading_id' => $trading->id,
                            'pricing_id' => $pricing->id,
                            'amount' => $pricing->amount,
                        ]);
                        $pricing->amount = 0;
                        $pricing->saveOrFail();
                    }
                }
                $trading->commodity->amount -= $attributes['amount'];
            }
            $trading->order->actual += ($attributes['amount'] * $attributes['price']) * ($trading->order->discount / 100);
            $trading->order->profit = $trading->order->actual - $trading->order->cost;
            $trading->commodity->saveOrFail();
            $trading->order->saveOrFail();
            DB::commit();
            return stored($trading);
        } catch (Throwable $exception) {
            DB::rollBack();
            return failed($exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Trading $trading
     * @return JsonResponse
     */
    public function show(Trading $trading)
    {
        return success($trading);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Trading $trading
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function destroy(Trading $trading)
    {
        DB::transaction(function () use($trading) {
            if ($trading->order->type === '采购') {
                $trading->order->total -= $trading->total;
                $pricing = Pricing::where('trading_id', $trading->id)->firstOrFail();
                $trading->order->cost -= $pricing->amount * $pricing->buying;
                $pricing->delete();

                $trading->order->actual -= ($trading->total / ($trading->order->discount / 100));

                $trading->commodity->amount -= $trading->amount;

            } elseif ($trading->order->type === '销售') {
                $trading->order->total -= $trading->total;
                /**
                 * @var Inventory[] $inventories
                 */
                $inventories = $trading->inventories()->get();
                $cost = 0;
                foreach ($inventories as $inventory) {
                    $inventory->pricing->amount += $inventory->amount;
                    $inventory->pricing->saveOrFail();
                    $cost += $inventory->amount * $inventory->pricing->buying;
                    $inventory->delete();
                }

                $trading->order->cost -= $cost;
                $trading->order->actual -= ($trading->total / ($trading->order->discount / 100));
                $trading->order->profit = $trading->order->actual - $trading->order->cost;

                $trading->commodity->amount += $trading->amount;
            }

            $trading->order->saveOrFail();
            $trading->commodity->saveOrFail();
            $trading->delete();
        });
        return deleted();
    }
}
