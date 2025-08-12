<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Class MessageTemplate
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $content
 * @property array|null $variables
 * @property string $type
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class MessageTemplate extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'content',
        'variables',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Template type constants
     */
    public const TYPE_SMS = 'sms';
    public const TYPE_EMAIL = 'email';
}
