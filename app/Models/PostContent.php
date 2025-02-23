<?php

namespace App\Models;


use App\Models\Scopes\PostContentScope;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Enums\PostContent\Status;
use App\Enums\PostContent\Type;
use App\Enums\Platform;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PostContent extends Model implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use InteractsWithMedia;

    protected $with = ['media'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'post_id',
        'type',
        'content',
        'url',
        'platform',
        'platform_id',
        'status',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => Status::class,
            'platform' => Platform::class,
            'published_at' => 'datetime',
            'type' => Type::class,
        ];
    }

    /**
     * The "booted" method of the model.x5m
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new PostContentScope());
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

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
