<?php

namespace App\Models;

use App\Enums\User\Role;

use function Illuminate\Events\queueable;

use App\Models\Traits\WorkspaceUsage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Laravel\Pennant\Feature;
use Laravel\Pennant\Concerns\HasFeatures;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Laravel\Cashier\Billable;

class Workspace extends Model implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use Billable;
    use WorkspaceUsage;
    use HasFeatures;
    use InteractsWithMedia;

    protected $with = ['plan'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updated(queueable(function (Workspace $workspace) {
            if ($workspace->hasStripeId()) {
                $workspace->syncStripeCustomerDetails();
            }
        }));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'plan_id',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'logo_url'
    ];

    public function getFeaturesAttribute()
    {
        return Feature::all();
    }

    public function updateFeatureFlags()
    {
        foreach ($this->features as $key => $feature) {
            Feature::for($this)->forget($key);
        }
    }

    /**
     * Get the workspace name that should be synced to Stripe.
     */
    public function stripeName(): string|null
    {
        return $this->name;
    }

    /**
     * Get the customer name that should be synced to Stripe.
     */
    public function stripeEmail(): string|null
    {
        return $this->users()->where('users.role', Role::OWNER)->first()->email;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile();
    }

    public function getLogoUrlAttribute()
    {
        return $this->hasMedia('logo')
            ? $this->getFirstMediaUrl('logo')
            :
            "https://api.dicebear.com/7.x/initials/svg?backgroundType=gradientLinear&fontFamily=Helvetica&fontSize=40&seed=" . urlencode($this->name);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function postContents(): HasMany
    {
        return $this->hasMany(PostContent::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function hashtags(): HasMany
    {
        return $this->hasMany(Hashtag::class);
    }
}
