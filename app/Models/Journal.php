<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    /**
     * Define user operation list
     *
     * @var array[] $actions
     */
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
        'commodity/image' => [
            'target' => '商品图片',
            'actions' => [
                'POST' => '上传'
            ]
        ],
        'discount' => [
            'target' => '商品价格',
            'actions' => [
                'POST' => '创建',
                'PUT' => '修改',
                'DELETE' => '删除'
            ]
        ],
        'profile/address' => [
            'target' => '客户地址',
            'actions' => [
                'POST' => '修改'
            ]
        ],
        'profile/avatar' => [
            'target' => '客户头像',
            'actions' => [
                'POST' => '上传'
            ]
        ],
        'profile/password' => [
            'target' => '用户密码',
            'actions' => [
                'POST' => '修改'
            ]
        ],
        'auth/signin' => [
            'target' => '自己',
            'actions' => [
                'POST' => '登录'
            ]
        ],
        'auth/issue' => [
            'target' => '自己',
            'actions' => [
                'POST' => 'Google 登录'
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
     * Date: 2020/5/23
     * @return MorphTo
     * @author George
     */
    public function journalable()
    {
        return $this->morphTo('journalable', 'user_type', 'user_id');
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
