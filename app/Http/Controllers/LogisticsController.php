<?php

namespace App\Http\Controllers;

use App\Models\Logistics;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class LogisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        //
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
            'order_id' => 'required|integer',
            'type' => 'required|in:寄付,到付',
            'receiver' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'remark' => 'nullable|string',
        ]);

        $logistic = Logistics::create($attributes);
        return stored($logistic);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Logistics $logistic
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Logistics $logistic, Request $request)
    {
        $attributes = $request->validate([
            'order_id' => 'required|integer',
            'type' => 'required|in:寄付,到付',
            'receiver' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'remark' => 'nullable|string',
        ]);

        $logistic->update($attributes);
        return updated($logistic);
    }

    /**
     * Date: 2020/5/18
     * @param Logistics $logistic
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function number(Logistics $logistic, Request $request)
    {
        $attributes = $request->validate([
            'number' => 'required|string',
            'company' => 'required|string'
        ]);

        $logistic->order()->update(['status' => 'completed']);
        $logistic->update($attributes);
        return updated($logistic);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
