<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUserType
{
    /**
     * 获取用户类型
     *
     * Date: 2020/5/22
     * @return string
     * @author George
     */
    public function getUserType(): string
    {
        $class = get_class($this);
        return Str::lower(class_basename($class));
    }
}
