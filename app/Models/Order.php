<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * 订单数据模型
 *
 * Date: 2019/12/21
 *
 * @author George
 * @package App\Models
 * @property int $id
 * @property string $type 交易类型：收入、支出
 * @property string $remark 用途：进货、出货、邮费
 * @property float $money 总金额
 * @property float $profit 总利润
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
}
