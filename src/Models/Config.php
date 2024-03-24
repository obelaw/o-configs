<?php

namespace Obelaw\Configs\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'path';
    protected $table = 'o_configs';

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
