<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Scopes\PlanScope;

class Plan extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'internal_id',
        'price',
        'is_monthly',
        'stripe_id',
        'access_level',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new PlanScope);
    }
}
