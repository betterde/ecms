<?php

namespace App\Http\Controllers;

use Exception;
use Ramsey\Uuid\Uuid;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
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
        $scene = $request->get('scene', 'table');
        $sort = $request->get('sort', 'created_at');
        $descend = (boolean)$request->post('descend', true);
        $query = Customer::query();
        $query->when($search = $request->get('search'), function (Builder $query, $search) {
            return $query->where('name', 'like', "%$search%");
        });

        switch ($scene) {
            case 'select':
                return success($query->get());
            case 'table':
            default:
            if ($descend) {
                $query->orderByDesc($sort);
            } else {
                $query->orderBy($sort);
            }

            return success($query->paginate($size));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Exception
     */
    public function store(Request $request)
    {
        $attributes = $this->validate($request, [
            'name' => 'required',
            'email' => 'nullable|unique:customers',
            'mobile' => 'required|unique:customers',
            'vip' => 'nullable|boolean',
            'province' => 'nullable',
            'municipality' => 'nullable',
            'prefecture' => 'nullable',
            'address' => 'nullable',
            'referrer' => 'nullable',
            'remark' => 'nullable'
        ]);

        $attributes['id'] = Uuid::uuid4()->toString();

        $customer = Customer::create($attributes);
        return stored($customer);
    }

    /**
     * Display the specified resource.
     *
     * @param Customer $customer
     * @return JsonResponse
     */
    public function show(Customer $customer)
    {
        $customer->orders = $customer->orders()->get();
        return success($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Customer $customer
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Customer $customer)
    {
        $attributes = $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('customers')->ignoreModel($customer)
            ],
            'mobile' => [
                'required',
                Rule::unique('customers')->ignoreModel($customer)
            ],
            'vip' => 'required|boolean',
            'province' => 'nullable',
            'municipality' => 'nullable',
            'prefecture' => 'nullable',
            'address' => 'nullable',
            'referrer' => 'nullable',
            'remark' => 'nullable'
        ]);

        $customer->update($attributes);
        return updated($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return deleted();
    }
}
