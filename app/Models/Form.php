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
        'active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'fields' => 'array',
        'active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
