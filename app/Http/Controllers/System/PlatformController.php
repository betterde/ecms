<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * Date: 2020/6/1
 * @author George
 * @package App\Http\Controllers\System
 */
class PlatformController extends Controller
{
    /**
     * Date: 2020/6/1
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function status(Request $request)
    {
        return success([
            'google' => config('services.google.enable')
        ]);
    }
}
