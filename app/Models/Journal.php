<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * User journal model
 *
 * Date: 2020/5/22
 * @author George
 * @package App\Models
 * @mixin Eloquent
 */
class Journal extends Model
{
    /**
     * @var string[] $guarded
     */
    protected $guarded = ['id'];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        'query' => 'json',
        'params' => 'json'
    ];

    protected static $actions = [
        'order' => [
            'target' => '订单',
            'actions' => [
                'POST' => '创建',
                'PUT' => '修改',
                'DELETE' => '删除'
            ]
        ],
        'commodity' => [
            'target' => '商品',
            'actions' => [
                'POST' => '创建',
                'PUT' => '修改',
                'DELETE' => '删除'
            ]
        ],
        'customer' => [
            'target' => '客户',
            'actions' => [
                'POST' => '创建',
                'PUT' => '修改',
                'DELETE' => '删除'
            ]
        ],
        'logistics' => [
            'target' => '物流',
            'actions' => [
                'POST' => '创建',
                'PUT' => '修改',
                'DELETE' => '删除'
            ]
        ],
        'trading' => [
            'target' => '交易',
            'actions' => [
                'POST' => '创建',
                'PUT' => '修改',
                'DELETE' => '删除'
            ]
        ],
        'invitation' => [
            'target' => '邀请',
            'actions' => [
                'POST' => '创建',
                'PUT' => '修改',
                'DELETE' => '删除'
            ]
        ],
        'auth/signin' => [
            'target' => null,
            'actions' => [
                'POST' => '登录'
            ]
        ]
    ];

    public static function getOperation(Request $request)
    {
        $path = $request->path();
        $path = substr($path, 4);
        $operation = self::$actions[$path];
        return [
            'action' => $operation['actions'][$request->method()],
            'target' => $operation['target']
        ];
    }

    /**
     * Date: 2020/5/22
     * @param DateTimeInterface $dateTime
     * @return string
     * @author George
     */
    protected function serializeDate(DateTimeInterface $dateTime)
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
