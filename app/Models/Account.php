<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Enums\Platform;
use App\Enums\Account\Status;

class Account extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'workspace_id',
        'space_id',
        'platform',
        'platform_id',
        'name',
        'username',
        'photo',
        'access_token',
        'refresh_token',
        'expires_in',
        'status',
        'is_verified'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'platform' => Platform::class,
            'status' => Status::class,
            'is_verified' => 'boolean',
        ];
    }

    protected $hidden = [
        'access_token',
        'refresh_token',
        'expires_in',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }
}
