<?php

namespace Obelaw\Configuration\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'path';
    protected $table = 'o_configurations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'value',
    ];

    protected $casts = [
        'value' => 'json',
    ];
}
