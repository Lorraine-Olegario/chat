<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'owner_id',
        'type',
    ];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('joined_at') // Adiciona o campo "joined_at" no relacionamento
                    ->withTimestamps(); // Garante que os timestamps created_at/updated_at serão gerenciados
    }
}
