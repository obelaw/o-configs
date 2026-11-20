<?php

namespace Obelaw\Configs\Models;

use Obelaw\Twist\Base\BaseModel;

/**
 * One configuration value. `group` says whose value it is: '' for the global
 * values, or a key such as "App\Models\Tenant:5" (see HasConfigs).
 *
 * @property int $id
 * @property string $group
 * @property string $path
 * @property mixed $value
 */
class Config extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'o_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group',
        'path',
        'value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
