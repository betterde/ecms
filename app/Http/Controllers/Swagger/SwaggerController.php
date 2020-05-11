<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 * Swagger Document Controller
 *
 * Date: 2020/5/11
 * @author George
 * @package App\Http\Controllers\Swagger
 */
class SwaggerController extends Controller
{
    public function index(string $version)
    {
        $url = url("/swagger/$version.yaml");
        return view('swagger', compact('url'));
    }
}
