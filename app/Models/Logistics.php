<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * 物流信息数据模型
 *
 * Date: 2020/5/15
 * @author George
 * @package App\Models
 * @mixin Eloquent
 */
class Logistics extends Model
{
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * 格式化日期数据
     *
     * Date: 2020/5/15
     * @param DateTimeInterface $dateTime
     * @return string
     * @author George
     */
    public function serializeDate(DateTimeInterface $dateTime)
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
