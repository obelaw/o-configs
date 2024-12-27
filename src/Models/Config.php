<?php

namespace Obelaw\Configs\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $path
 * @property array $value
 */
class Config extends Model
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
