<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * User certificate data model
 *
 * Date: 2020/5/28
 * @author George
 * @package App\Models
 * @mixin Eloquent
 */
class Certificate extends Model
{
    /**
     * @var string[] $guarded
     */
    protected $guarded = ['id'];

    /**
     * Defines the association that gets the certificate owner
     *
     * Date: 2020/5/28
     * @return MorphTo
     * @author George
     */
    public function ownerable()
    {
        return $this->morphTo('ownerable', 'owner_type', 'owner_id');
    }
}
