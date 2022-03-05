<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'status',
    ];

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    const STATUSES_NAMES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function getStatusNameAttribute()
    {
        return self::STATUSES_NAMES[$this->status];
    }
}
