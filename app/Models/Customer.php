<?php

namespace App\Models;

use App\Contracts\UserTypeInterface;
use App\Traits\HasUserType;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * 客户数据模型
 *
 * Date: 2019/12/8
 *
 * @author George
 * @package App\Models
 * @property string $id UUID
 * @property string $name 姓名
 * @property string|null $email 邮箱
 * @property string|null $mobile 手机号
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
 * @method static Customer create(array $attributes = [])
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer query()
 * @method static Builder|Customer whereAddress($value)
 * @method static Builder|Customer whereBalance($value)
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereEmail($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereMobile($value)
 * @method static Builder|Customer whereMunicipality($value)
 * @method static Builder|Customer whereName($value)
 * @method static Builder|Customer wherePassword($value)
 * @method static Builder|Customer wherePrefecture($value)
 * @method static Builder|Customer whereProvince($value)
 * @method static Builder|Customer whereReferrer($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @method static Builder|Customer whereVip($value)
 * @mixin \Eloquent
 */
class Customer extends Authenticatable implements JWTSubject, UserTypeInterface
{
    use Notifiable, HasUserType;

    /**
     * @var string $primaryKey
     */
    protected $primaryKey = 'id';

    /**
     * @var bool $incrementing
     */
    public $incrementing = false;

    /**
     * @var array $guarded
     */
    protected $guarded = [];

    /**
     * Define orders relation
     *
     * Date: 2020/4/19
     * @return HasMany
     * @author George
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }

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

    /**
     * Date: 2020/5/11
     * @return MorphMany
     * @author George
     */
    public function invitations()
    {
        return $this->morphMany(Invitation::class, 'initiator');
    }

    /**
     * Date: 2020/5/12
     * @param DateTimeInterface $dateTime
     * @return string
     * @author George
     */
    protected function serializeDate(DateTimeInterface $dateTime)
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
