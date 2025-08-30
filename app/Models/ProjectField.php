<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectField extends Model
{
    //

    protected $fillable = [
        'category',
        'field_name',
        'label',
        'type',
        'options',
        'required',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];
}

