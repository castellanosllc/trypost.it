<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Space extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $fillable = [
        'workspace_id',
        'name',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('media-library');
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
