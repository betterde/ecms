<?php

namespace App\Models;

use Eloquent;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 商品信息数据模型
 *
 * Date: 2020/2/4
 * @author George
 * @package App\Models
 * @property Pricing[] $pricings
 * @method static Commodity create(array $attributes = [])
 * @mixin Eloquent
 */
class Commodity extends Model
{
    /**
     * @var array $guarded
     */
    protected $guarded = ['id'];

    /**
     * Define trading relation
     *
     * Date: 2020/2/5
     * @return HasOne
     * @author George
     */
    public function trading()
    {
        return $this->hasOne(Trading::class, 'commodity_id', 'id');
    }

    /**
     * Date: 2020/5/14
     * @param $image
     * @return string
     * @author George
     */
    public function getImageAttribute($image)
    {
        return url($image);
    }

    /**
     * Define commodity pricings history relation
     *
     * Date: 2020/2/7
     * @return HasMany
     * @author George
     */
    public function pricings()
    {
        return $this->hasMany(Pricing::class, 'commodity_id', 'id');
    }
}
