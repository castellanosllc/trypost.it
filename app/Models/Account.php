<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use App\Enums\Account\Platform;
use App\Enums\Account\Status;

class Account extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'workspace_id',
        'platform',
        'platform_id',
        'username',
        'photo',
        'access_token',
        'refresh_token',
        'expires_in',
        'status',
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
        ];
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
