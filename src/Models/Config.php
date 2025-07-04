<?php

namespace Obelaw\Configs\Models;

use Obelaw\Twist\Base\BaseModel;

/**
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
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'path';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
