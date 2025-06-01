<?php

namespace Obelaw\Configs\Models;

use Obelaw\Twist\Base\BaseModel;

class ConfigModel extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'o_config_models';

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

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function modelable()
    {
        return $this->morphTo();
    }
}
