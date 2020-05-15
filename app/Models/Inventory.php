<?php

namespace App\Models;

use Eloquent;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Date: 2020/2/12
 * @author George
 * @package App\Models
 * @property Pricing|null $pricing
 * @method static Inventory create(array $attributes = [])
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
