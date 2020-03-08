<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Commodity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

/**
 * Commodity information logic controller
 *
 * Date: 2020/2/4
 * @author George
 * @package App\Http\Controllers
 */
class CommodityController extends Controller
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
        $sort = $request->get('sort', 'id');
        $scene = $request->get('scene', 'table');
        $descend = (boolean)$request->get('descend', false);
        $query = Commodity::query();

        $query->when($search = $request->get('search'), function (Builder $query, $search) {
            /**
             * @var Builder $query
             */
            return $query->where('name', 'like', "%$search%")
                ->orWhere('brand', 'like', "%$search%")
                ->orWhere('category', 'like', "%$search%");
        });

        switch ($scene) {
            case 'select':
                $collections = $query->get();
                $options = $collections->groupBy('category');
                $result = [];
                foreach ($options as $category => $commodities) {
                    $result[] = [
                        'label' => $category,
                        'options' => $commodities,
                    ];
                }
                return success($result);
            case 'table':
            default:
                $query->when($brand = $request->get('brand'), function (Builder $query, $brand) {
                    /**
                     * @var Builder $query
                     */
                    return $query->where('brand', $brand);
                });

                $query->when($category = $request->get('category'), function (Builder $query, $category) {
                    /**
                     * @var Builder $query
                     */
                    return $query->where('category', $category);
                });


                $query->when($request->get('zero') === 'true', function (Builder $query) {
                    /**
                     * @var Builder $query
                     */
                    return $query->where('amount', 0);
                });


                if ($descend) {
                    $query->orderByDesc($sort);
                } else{
                    $query->orderBy($sort);
                }

                return success($query->paginate($size));
        }
    }

    /**
     * 获取库存商品的品牌列表信息
     *
     * Date: 2019/12/28
     * @return JsonResponse
     * @author George
     */
    public function brand()
    {
        $brands = Commodity::select(['brand as name'])->groupBy('brand')->get();
        return success($brands);
    }

    /**
     * 获取库存商品的分类列表信息
     *
     * Date: 2019/12/28
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function category(Request $request)
    {
        $brands = Commodity::select(['category as name'])->groupBy('category')->get();
        return success($brands);
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
            'brand' => 'required|string',
            'name' => 'required|string',
            'unit' => 'required|string',
            'specification' => 'nullable|string',
            'category' => 'required|string',
            'remark' => 'nullable|string',
            'image' => 'nullable|string',
            'barcode' => 'nullable|string',
            'description' => 'nullable|string'
        ]);
        $commodity = Commodity::create($attributes);
        return stored($commodity);
    }

    /**
     * Display the specified resource.
     *
     * @param Commodity $commodity
     * @return JsonResponse
     */
    public function show(Commodity $commodity)
    {
        $commodity->pricings = $commodity->pricings()->get();
        return success($commodity);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Commodity $commodity
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Commodity $commodity)
    {
        $attributes = $this->validate($request, [
            'brand' => 'required|string',
            'name' => 'required|string',
            'unit' => 'required|string',
            'specification' => 'nullable|string',
            'category' => 'required|string',
            'remark' => 'nullable|string',
            'image' => 'nullable|string',
            'barcode' => 'nullable|string',
            'description' => 'nullable|string'
        ]);
        $commodity->update($attributes);
        return updated($commodity);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Commodity $commodity
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Commodity $commodity)
    {
        if ($commodity->amount > 0) {
            return failed("当前商品还有库存无法删除", 422);
        }
        $commodity->delete();
        return deleted();
    }
}
