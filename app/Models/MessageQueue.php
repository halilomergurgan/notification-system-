<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Class MessageQueue
 *
 * @property int $id
 * @property int $message_id
 * @property int $recipient_id
 * @property string|null $personalized_content
 * @property string $status
 * @property int $retry_count
 * @property int $max_retries
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $sent_at
 * @property string|null $provider_message_id
 * @property array|null $provider_response
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Message $message
 * @property-read Recipient $recipient
 *
 * @package App\Models
 */
class MessageQueue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'message_id',
        'recipient_id',
        'personalized_content',
        'status',
        'retry_count',
        'max_retries',
        'scheduled_at',
        'sent_at',
        'provider_message_id',
        'provider_response',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'provider_response' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
    ];

    /**
     * Status constants
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the message that owns the queue.
     *
     * @return BelongsTo
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the recipient that owns the queue.
     *
     * @return BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }
}
