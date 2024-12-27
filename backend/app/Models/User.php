<?php

namespace App\Models;

use OpenApi\Attributes as OA;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

#[
    OA\Schema(
        properties: [
            new OA\Property(property: 'name', type: 'string', example: "John Doe"),
            new OA\Property(property: 'birthday', type: 'string', format: 'date-time', example: "2023-10-01T12:00:00Z"),
            new OA\Property(property: 'gender', type: 'integer', enum: [User::FEMALE, User::MALE, User::OTHER], example: User::FEMALE),
            new OA\Property(property: 'email', type: 'string', example: "john.doe@example.com"),
            new OA\Property(property: 'user_name', type: 'string', example: "johndoe"),
            new OA\Property(property: 'is_banned', type: 'boolean', example: false),
            new OA\Property(property: 'avatar', type: 'string', example: "https://example.com/avatar.jpg"),
        ]
    )
]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Searchable;


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
        'birthday',
        'gender',
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

    public function followers()
    {
        return $this->belongsToMany(User::class, 'following_users', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'following_users', 'follower_id', 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'following_tags', 'follower_id', 'tag_id');
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Article::class, 'bookmarks', 'user_id', 'article_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'user_id', 'id');
    }

    public function searchableAs()
    {
        return 'users_index';
    }

    public function toSearchableArray()
    {
        return [
            'user_name' => $this->user_name,
            'name' => $this->name
        ];
    }

    public function shouldBeSearchable()
    {
        return $this->is_banned == false;
    }

    protected function avatar(): Attribute
    {
        return Attribute::get(fn(?string $avatar) => $avatar ? sprintf("%s/%s/%s", env('AWS_ENDPOINT'), env('AWS_BUCKET'), $avatar) : null);
    }
}
