<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Date: 2020/5/19
 * @author George
 * @package App\Models
 * @mixin Eloquent
 */
class Discount extends Model
{
    protected $guarded = ['id'];

    /**
     * Date: 2020/5/19
     * @param DateTimeInterface $dateTime
     * @return string
     * @author George
     */
    protected function serializeDate(DateTimeInterface $dateTime)
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
