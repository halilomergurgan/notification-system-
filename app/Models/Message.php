<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Class Message
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $type
 * @property int $character_count
 * @property int $sms_count
 * @property array|null $variables
 * @property bool $is_active
 * @property Carbon|null $scheduled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property int $template_id
 *
 * @property-read MessageQueue[] $messageQueues
 *
 * @package App\Models
 */
class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'content',
        'type',
        'character_count',
        'sms_count',
        'variables',
        'is_active',
        'scheduled_at',
        'template_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'scheduled_at' => 'datetime',
        'character_count' => 'integer',
        'sms_count' => 'integer',
    ];

    /**
     * Message type constants
     */
    public const TYPE_SMS = 'sms';
    public const TYPE_EMAIL = 'email';

    /**
     * Get the message queues for the message.
     *
     * @return HasMany
     */
    public function messageQueues(): HasMany
    {
        return $this->hasMany(MessageQueue::class);
    }
}
