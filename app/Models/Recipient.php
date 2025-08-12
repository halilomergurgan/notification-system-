<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Class Recipient
 *
 * @property int $id
 * @property string $phone_number
 * @property string $country_code
 * @property string|null $name
 * @property string|null $email
 * @property bool $is_active
 * @property bool $is_blacklisted
 * @property Carbon|null $last_contact_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read MessageQueue[] $messageQueues
 *
 * @package App\Models
 */
class Recipient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'phone_number',
        'country_code',
        'name',
        'email',
        'is_active',
        'is_blacklisted',
        'last_contact_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_blacklisted' => 'boolean',
        'last_contact_at' => 'datetime',
    ];

    /**
     * Get the message queues for the recipient.
     *
     * @return HasMany
     */
    public function messageQueues(): HasMany
    {
        return $this->hasMany(MessageQueue::class);
    }

    /**
     * Get the full phone number with country code.
     *
     * @return string
     */
    public function getFullPhoneNumberAttribute(): string
    {
        return $this->country_code . $this->phone_number;
    }
}
