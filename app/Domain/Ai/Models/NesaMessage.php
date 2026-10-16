<?php

declare(strict_types=1);

namespace App\Domain\Ai\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $conversation_id
 * @property string $role
 * @property string $content
 * @property array<array-key, mixed>|null $tool_calls
 * @property string|null $tool_call_id
 * @property array<array-key, mixed>|null $recommended_listing_ids
 * @property int|null $tokens_used
 * @property int|null $latency_ms
 * @property Carbon|null $created_at
 * @property-read NesaConversation $conversation
 */
class NesaMessage extends Model
{
    use HasUuids;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'tool_calls',
        'tool_call_id',
        'recommended_listing_ids',
        'tokens_used',
        'latency_ms',
        'created_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if ($model->created_at === null) {
                $model->created_at = now();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tool_calls' => 'array',
            'recommended_listing_ids' => 'array',
            'tokens_used' => 'integer',
            'latency_ms' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<NesaConversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(NesaConversation::class, 'conversation_id');
    }
}
