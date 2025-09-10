<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "age",
        "gender",
        "number",
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }

    /**
     * get the profile image
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * get user created addresses
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * get the user store
     */
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    /**
     * get the user products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * get the user comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * get the user bazaars
     */
    public function bazaars()
    {
        return $this->hasMany(Bazaar::class);
    }
}
