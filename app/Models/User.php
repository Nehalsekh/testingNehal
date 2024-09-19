<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'invite_token',
        'is_publisher',
        'is_client',
        'country_id',
        'allow_bank_transactions',
        'paypal_invoice',
        'added_by',
        'skype_id',
        'tds_tax',
        'status'
    ];

    protected $guarded = [];
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $attributes=[];

    public function isTechnicalAdmin(): bool
    {
        return true;
    }

    public function additionalInfo(): HasOne
    {
        return $this->hasOne(UserAdditionalInformation::class,'user_id','id');
    }

    public function team(): BelongsToMany
    {
        return $this->belongsToMany(Team::class,'team_users','user_id','team_id')
            ->withPivot('id','is_admin')
            ->withTimestamps();
    }

    public function emails(): BelongsToMany
    {
        return $this->belongsToMany(Email::class,'email_users')
            ->withPivot('id','is_admin','read','send','send_as_alias')
            ->withTimestamps();
    }

    public function vpa():HasOne
    {
        return $this->hasOne(Vpa::class,'user_id','id');
    }

    public function bankAccount():HasOne
    {
        return $this->hasOne(BankAccount::class,'user_id','id');
    }

    public function publisherSite():HasMany
    {
        return $this->hasMany(Site::class, 'owner_id', 'id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function scopeAdmin($query)
    {
        // return $query; // bypass filter
        return $query->withCount('roles')->has('roles', '>=', 1);
    }

    public function scopeClient($query)
    {
        // return $query; // bypass filter
        return $query->where('is_client','=',1);
    }


    public function walletUser():BelongsToMany
    {
        return $this->belongsToMany(User::class,'user_wallets','user_id','wallet_id')
            ->withTimestamps();
    }

    public function company(): BelongsTo
    {
        return $this->BelongsTo(BoCompany::class, 'company_id', 'id');
    }
}
