<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'customer_id',
        'review',
        'rate',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
