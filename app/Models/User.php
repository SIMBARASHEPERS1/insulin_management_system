<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

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

    public function phone(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ?: 'Not Provided',
//            set: fn($value) => $value ? Str::of($value)->replaceMatches('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3') : "Not Provided"
        );
    }

    public function lastEntry(): Attribute
    {
        $activity = $this->activity->last();
        return Attribute::make(
            get: fn() => $activity ? Carbon::parse($activity->created_at)->diffForHumans() : 'No Entry',
        );
    }

    public function lastSugar(): Attribute
    {
        $activity = $this->activity->last();
        return Attribute::make(
            get: fn() => $activity ? $activity->sugar_level : 'No Entry',
        );
    }

    public function lastActivity(): Attribute
    {
        $activity = $this->activity->last();
        return Attribute::make(
            get: fn() => $activity ? $activity->protocol : 'No Entry',
        );
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function likes(): BelongsToMany
    {
        return $this->BelongsToMany(Product::class, 'products_likes');
    }

    public function patientInformation(): HasMany
    {
        return $this->hasMany(PatientInformation::class, 'patient_id', 'id');
    }

    public function patientAthrometric(): HasMany
    {
        return $this->hasMany(PatientAthrometric::class, 'patient_id', 'id');
    }

    public function patientPhysiology(): HasMany
    {
        return $this->hasMany(PatientPhysiology::class, 'patient_id', 'id');
    }

    public function activity(): HasMany
    {
        return $this->hasMany(PatientActivity::class, 'patient_id', 'id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(PatientActivityHistory::class, 'patient_id', 'id');
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => ucfirst(str($this->name)->explode(' ')->first())
        );
    }

    protected function IsAdmin(): Attribute
    {
        return Attribute::make(
            get: fn(?bool $value) => $this->role === 'admin'
        );
    }
}
