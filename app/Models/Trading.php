<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Trading
 *
 * @property int $id
 * @property string $purchaser 消费者ID
 * @property float $total 总价
 * @property float $postage 邮费
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Order|null $order 商品信息模型
 * @property Commodity|null $commodity 商品信息模型
 * @property Inventory[]|null $inventories
 * @method static Trading create(array $attributes = [])
 * @method static Builder|Trading newModelQuery()
 * @method static Builder|Trading newQuery()
 * @method static Builder|Trading query()
 * @method static Builder|Trading whereAmount($value)
 * @method static Builder|Trading whereCreatedAt($value)
 * @method static Builder|Trading whereId($value)
 * @method static Builder|Trading whereInventoryId($value)
 * @method static Builder|Trading whereOrderId($value)
 * @method static Builder|Trading wherePrice($value)
 * @method static Builder|Trading whereTotal($value)
 * @method static Builder|Trading whereType($value)
 * @method static Builder|Trading whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Trading extends Model
{
    /**
     * @var array $guarded
     */
    protected $guarded = ['id'];

    /**
     * @var array $casts
     */
    protected $casts = [
        'amount' => 'integer',
//        'price' => 'float',
//        'total' => 'float',
    ];

    /**
     * Order model
     *
     * Date: 2019/12/31
     * @return BelongsTo
     * @author George
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Commodity model
     *
     * Date: 2020/2/5
     * @return BelongsTo
     * @author George
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'commodity_id', 'id', 'commodity');
    }

    /**
     * Date: 2020/3/5
     * @return HasMany
     * @author George
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'trading_id', 'id');
    }
}
