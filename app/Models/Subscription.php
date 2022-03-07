<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'plan_id',
        'price',
        'duration',
        'start_date',
        'end_date',
        'payment_id',
        'payment_status',
        'status',
    ];

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    const SATAUTS_NAMES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    const PAYMENT_STATUS_CREATED = 'created';

    const PAYMENT_STATUS_APPROVED = 'approved';

    const PAYMENT_STATUS_FAILED = 'failed';

    const PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_CREATED,
        self::PAYMENT_STATUS_APPROVED,
        self::PAYMENT_STATUS_FAILED,
    ];

    protected static function booted()
    {
        static::saved(function ($subscription) {
            if ($subscription->status === self::STATUS_ACTIVE) {
                $subscription->profile->update(['order' => Profile::ORDER_ACTIVE]);
            } else {
                $subscription->profile->update(['order' => Profile::ORDER_INACTIVE]);
            }
        });
    }

    public function getStatusNameAttribute()
    {
        return self::SATAUTS_NAMES[$this->status];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
