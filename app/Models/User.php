<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use App\Models\Traits\HasWorkspaces;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasFactory;
    use Notifiable;
    use HasUuids;
    use HasWorkspaces;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'language_id',
        'current_workspace_id',
        'email_verified_at',
        'theme'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'photo_url',
        'first_name'
    ];

    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->singleFile();
    }

    public function getPhotoUrlAttribute()
    {
        return $this->hasMedia('photos')
            ? $this->getFirstMediaUrl('photos')
            :
            "https://api.dicebear.com/7.x/initials/svg?backgroundType=gradientLinear&fontFamily=Helvetica&fontSize=40&seed=" . urlencode($this->name);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
