<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * 客户数据模型
 *
 * Date: 2019/12/8
 *
 * @author George
 * @package App\Models
 * @property string $id
 * @property string $name
 * @property string|null $email
 * @property string|null $mobile
 * @property float $balance
 * @property string|null $password
 * @property int $vip 会员
 * @property string|null $province 省
 * @property string|null $municipality 市
 * @property string|null $prefecture 县
 * @property string|null $address 地址
 * @property string|null $referrer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereMunicipality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer wherePrefecture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereReferrer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Customer whereVip($value)
 * @mixin \Eloquent
 */
class Customer extends Authenticatable implements JWTSubject
{
    /**
     * @var bool $incrementing
     */
    public $incrementing = false;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'guard' => 'customer'
        ];
    }
}
