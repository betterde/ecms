<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 订单数据模型
 *
 * Date: 2019/12/21
 *
 * @author George
 * @package App\Models
 * @property int $id
 * @property string $type 交易类型：采购、销售
 * @property float $total 总金额
 * @property integer $discount 折扣
 * @property float $actual 实际金额
 * @property float $cost 成本
 * @property float $profit 总利润
 * @property string $date 日期
 * @property string $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Order create(array $attributes = [])
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereMoney($value)
 * @method static Builder|Order whereProfit($value)
 * @method static Builder|Order wherePurchaser($value)
 * @method static Builder|Order whereRemark($value)
 * @method static Builder|Order whereType($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Order extends Model
{
    /**
     * @var array $guarded
     */
    protected $guarded = ['id'];

    /**
     * @var array $casts
     */
    protected $casts = [
//        'total' => 'float'
    ];

    /**
     * Define trading relation
     *
     * Date: 2020/2/5
     * @return HasMany
     * @author George
     */
    public function tradings()
    {
        return $this->hasMany(Trading::class, 'order_id', 'id');
    }

    /**
     * Define customer relation
     *
     * Date: 2020/4/19
     * @return BelongsTo
     * @author George
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
