<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'user_id',
        'amount',
        'currency',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'status',
        'meta',
        'receipt_path'
    ];

    protected $casts = ['meta'=>'array'];

    public function submission(){
        return $this->belongsTo(Submission::class); 
    }

    public function user(){ 
        return $this->belongsTo(User::class);
    }
}
