<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pricing
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pricing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pricing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pricing query()
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
}
