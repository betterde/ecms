<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlatformController extends Controller
{
    public function status(Request $request)
    {
        return success([
            'google' => env('GOOGLE_SIGNIN')
        ]);
    }
}
