<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    protected $casts = [
        'from' => 'integer',
        'to' => 'integer',
        'read' => 'boolean',
        'received' => 'boolean',
        'sent' => 'boolean'
    ];
}
