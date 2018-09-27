<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Backpack\CRUD\CrudTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Stripe\Customer;
use Stripe\Subscription;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property string email
 * @property string stripe_id
 * @property Customer customer
 * @property string card_expiry
 * @property string card_brand
 * @property string card_last_four
 * @property Subscription subscription
 * @property string trial_ends_at
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, Billable, CrudTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'password',
        'role_id',
        'company_id',
        'verified',
        'card_expiry',
        'card_brand',
        'card_last_four',
        'trial_ends_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'first_name',
        'last_name',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'password',
        'company_id',
        'role_id',
        'verified',
        'trial_ends_at',
        'stripe_is_assigned',
        'full_name',

        'company',
        'role',
        'teams'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'stripe_is_assigned',
        'full_name'
    ];

    public function setTrialEndsAtAttribute($value)
    {
        $this->attributes['trial_ends_at'] = $value ? Carbon::createFromTimestamp($value)->toDateTimeString() : null;
    }

    /**
     * Relation with companies.
     */
    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'users_teams');
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return date("m/d/Y H:m", strtotime($value));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return strtolower($this->role->name) === strtolower($role);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketComments()
    {
        return $this->hasMany(TicketComment::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return Customer
     */
    public function getCustomerAttribute()
    {
        return Customer::retrieve($this->stripe_id);
    }

    /**
     * @return bool
     */
    public function getStripeIsAssignedAttribute()
    {
        return !empty($this->stripe_id);
    }

    /**
     * @return Subscription|null
     */
    public function getSubscriptionAttribute()
    {
        if (!$this->stripe_id) {
            return null;
        }

        $subscriptions = $this->customer->subscriptions;

        return count($subscriptions->data) ? $subscriptions->data[0] : null;
    }

    /**
     * @return bool
     */
    public function isSubscribed()
    {
        $subscription = $this->subscription;

        return $subscription &&
            ($subscription->status === 'active' || ($subscription->status === 'trialing' && $this->isOnTrial()));
    }

    /**
     * @return bool
     */
    public function isOnTrial()
    {
        if (!$this->trial_ends_at) {
            return false;
        }

        $currentDate = Carbon::now();
        $trialEndsAt = Carbon::createFromFormat('Y-m-d H:i:s', $this->trial_ends_at);

        return $currentDate->diffInHours($trialEndsAt, false) > 0;
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
