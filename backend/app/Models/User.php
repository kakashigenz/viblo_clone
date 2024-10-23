<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    public const USER_ROLE = 1;
    public const MOD_ROLE = 2;
    public const ADMIN_ROLE = 3;

    public const FEMALE = 0;
    public const MALE = 1;
    public const OTHER = 2;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_name',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
            'birthday' => 'datetime'
        ];
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'following_users', 'follower_id', 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'following_tags', 'follower_id', 'tag_id');
    }
}
