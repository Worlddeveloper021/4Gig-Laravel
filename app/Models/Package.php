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
    ];

    const ON_DEMAND_VALUES = ['available', 'unavailable', 'offline'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
