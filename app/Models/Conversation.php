<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *     schema="Conversation",
 *     type="object",
 *     title="Conversation",
 *     description="Conversation model schema",
 *     required={"name", "type", "participant_id"},
 *     
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the conversation",
 *         example="Conversa com irmã"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         format="type",
 *         description="Type conversation [private, group]",
 *         example="private"
 *     ),
 *     @OA\Property(
 *         property="participant_id",
 *         type="string",
 *         description="Second user identifier",
 *         example="5a047f80-8e0f-4081-b398-10ab80cda497"
 *     )
 * )
 */
class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'type',
    ];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Conversation::class)
                    ->withPivot('joined_at') // Adiciona o campo "joined_at" no relacionamento
                    ->withTimestamps(); // Garante que os timestamps created_at/updated_at serão gerenciados
    }
}
