<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Pricing
 *
 * @method static Pricing create(array $attributes = [])
 * @method static Builder|Pricing newModelQuery()
 * @method static Builder|Pricing newQuery()
 * @method static Builder|Pricing query()
 * @mixin Eloquent
 */
class Pricing extends Model
{
    /**
     * @var array $guarded
     */
    protected $guarded = ['id'];

    /**
     * @var array $casts
     */
    protected $casts = [
        'buying' => 'float'
    ];

    /**
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
