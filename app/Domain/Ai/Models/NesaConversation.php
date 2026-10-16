<?php

declare(strict_types=1);

namespace App\Domain\Ai\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property int|null $user_id
 * @property string $session_token
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @property-read Collection<int, NesaMessage> $messages
 */
class NesaConversation extends Model
{
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'session_token',
        'title',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'title' => 'Percakapan Baru',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<NesaMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(NesaMessage::class, 'conversation_id');
    }
}
