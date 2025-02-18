<?php

namespace App\Models;

use App\Enums\User\Role;

use function Illuminate\Events\queueable;

use App\Models\Traits\WorkspaceUsage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Laravel\Pennant\Feature;
use Laravel\Pennant\Concerns\HasFeatures;

use Laravel\Cashier\Billable;

class Workspace extends Model
{
    use HasFactory;
    use HasUuids;
    use Billable;
    use SoftDeletes;
    use WorkspaceUsage;
    use HasFeatures;
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
        return $this->users()->where('role', Role::OWNER)->first()->email;
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? asset($this->logo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=000000&length=1';
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->as('membership')
            ->withTimestamps();
    }

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function postStats(): HasMany
    {
        return $this->hasMany(PostStat::class);
    }
}
