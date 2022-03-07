<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'price',
        'duration',
        'description',
        'on_demand',
        'status',
    ];

    const ON_DEMAND_VALUES = ['available', 'unavailable', 'offline'];

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    const STATUS_NAMES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function getStatusNameAttribute()
    {
        return self::STATUS_NAMES[$this->status];
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
