<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model schema",
 *     required={"uuid", "name", "email"},
 *     
 *      @OA\Property(
 *         property="id",
 *         type="string",
 *         description="Identifier of the user",
 *         example="14"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the user",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user",
 *         example="johndoe@example.com"
 *     ),
 *     @OA\Property(
 *         property="number",
 *         type="string",
 *         description="Unique identifier of the user",
 *         example="123e4567-e89b-12d3-a456-426614174000"
 *     )
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
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
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Gera um UUID antes de criar o usuário
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid(); // Gera um UUID usando a classe Str do Laravel
        });
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)
                    ->withPivot('joined_at', 'alias')
                    ->withTimestamps(); // Garante que os timestamps created_at/updated_at serão gerenciados
    }
}
