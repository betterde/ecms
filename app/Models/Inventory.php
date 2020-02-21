<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Date: 2020/2/12
 * @author George
 * @package App\Models
 * @property Pricing|null $pricing
 * @mixin Eloquent
 */
class Inventory extends Model
{
    /**
     * @var array $guarded
     */
    protected $guarded = ['id'];

    /**
     * Disable timestamps
     *
     * @var bool $timestamps
     */
    public $timestamps = false;

    /**
     * Date: 2020/2/12
     * @return BelongsTo
     * @author George
     */
    public function pricing()
    {
        return $this->belongsTo(Pricing::class, 'pricing_id', 'id');
    }
}
