<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Date: 2020/5/11
 * @author George
 * @package App\Models
 * @mixin Eloquent
 */
class Invitation extends Model
{
    /**
     * @var string[] $guarded
     */
    protected $guarded = ['id'];

    /**
     * Date: 2020/5/11
     * @return MorphTo
     * @author George
     */
    public function initiator()
    {
        return $this->morphTo();
    }

    public function getExpiresAttribute($expires)
    {
        return date('Y-m-d H:i:s', $expires);
    }

    /**
     * Date: 2020/5/12
     * @param DateTimeInterface $dateTime
     * @return string
     * @author George
     */
    protected function serializeDate(DateTimeInterface $dateTime)
    {
        return $dateTime->format('Y-m-d');
    }
}
