<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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
     * @return JsonResponse|View
     * @author George
     */
    public function index()
    {
        $view = 'index';
        $file = sprintf('%s.blade.php', $view);
        $path = resource_path("views/{$file}");
        if (Storage::exists($path)) {
            return view($view);
        } else {
            return message('首页不存在，请安装前端依赖并执行 yarn build 命令，完成后即可访问。', 404);
        }
    }
}
