<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'user_id',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function form(){
        return $this->belongsTo(Form::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function payment(){
        return $this->hasOne(Payment::class);
    }
}
