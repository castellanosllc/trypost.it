<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

use App\Enums\Post\Status;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workspace_id',
        'space_id',
        'content',
        'scheduled_at',
        'status'
    ];

    protected $with = ['media'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => Status::class
        ];
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query
            ->where('status', Status::SCHEDULED)
            ->where('scheduled_at', '<=', now());
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', Status::PUBLISHED)
            ->where('scheduled_at', '<=', now());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('medias')
            ->onlyKeepLatest(5);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function postStats(): HasMany
    {
        return $this->hasMany(PostStat::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
