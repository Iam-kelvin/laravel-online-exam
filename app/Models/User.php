<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_handle',
        'email',
        'country',
        'state',
        'county',
        'level',
        'grade',
        'school',
        'school_level',
        'class_year',
        'country_of_study',
        'city_town',
        'password',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function communityPosts()
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function communityComments()
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function setDisplayHandleAttribute(?string $value): void
    {
        $this->attributes['display_handle'] = static::normalizeDisplayHandle($value);
    }

    public static function normalizeDisplayHandle(?string $value): ?string
    {
        $handle = trim((string) $value);
        $handle = ltrim($handle, '@');
        $handle = strtolower($handle);

        return $handle === '' ? null : $handle;
    }

    public function publicName(): string
    {
        if ($this->display_handle) {
            return '@' . $this->display_handle;
        }

        return $this->firstName();
    }

    public function firstName(): string
    {
        $name = trim((string) $this->name);

        if ($name === '') {
            return 'CrazyExam learner';
        }

        $parts = preg_split('/\s+/', $name) ?: [$name];

        return $parts[0] ?: $name;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasAnyRoles($roles)
    {
        if($this->roles()->whereIn('name', $roles)->first())
        {
            return true;
        }

        return false;
    }

    public function hasRole($role)
    {
        if($this->roles()->where('name', $role)->first())
        {
            return true;
        }

        return false;
    }
}
