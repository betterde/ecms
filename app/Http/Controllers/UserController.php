<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

/**
 * User logic controller
 *
 * Date: 2020/6/19
 * @author George
 * @package App\Http\Controllers
 */
class UserController extends Controller
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
        $query = User::query();

        $query->when($search = $request->get('search'), function (Builder $query, $search) {
            return $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        });

        switch ($scene) {
            case 'select':
                $query->select(['id', 'name']);
                $users = $query->get();
                return success($users);
            case 'table':
            default:
                $query->when($brand = $request->get('brand'), function (Builder $query, $brand) {
                    return $query->where('brand', $brand);
                });

                $query->when($category = $request->get('category'), function (Builder $query, $category) {
                    return $query->where('category', $category);
                });

                $query->when($request->get('zero') === 'true', function (Builder $query) {
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return Response
     */
    public function destroy(User $user)
    {
        //
    }
}
