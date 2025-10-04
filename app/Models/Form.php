<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'fields',
        'created_by',
    ];

    protected $casts = [
        'fields' => 'array'
    ];
}
