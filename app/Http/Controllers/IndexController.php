<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;

/**
 * Index view controller
 *
 * Date: 2020/5/21
 * @author George
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * Date: 2020/5/21
     * @return JsonResponse|\Illuminate\View\View
     * @author George
     */
    public function index()
    {
        $view = 'index';
        if (View::exists($view)) {
            return view($view);
        } else {
            return message('首页不存在，请安装前端依赖并执行 yarn build 命令，完成后即可访问。');
        }
    }
}
