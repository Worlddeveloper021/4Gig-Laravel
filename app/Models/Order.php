<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'profile_id',
        'package_id',
        'payment_id',
        'payment_status',
        'duration',
        'price',
        'status',
        'channel_name',
    ];

    const STATUS_PENDING = 0;

    const STATUS_DONE = 1;

    const STATUS_CANCELED = 2;

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_DONE,
        self::STATUS_CANCELED,
    ];

    const STATUS_NAMES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_DONE => 'Done',
        self::STATUS_CANCELED => 'Canceled',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUS_NAMES[$this->status];
    }
}
