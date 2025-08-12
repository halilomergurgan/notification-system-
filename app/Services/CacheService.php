<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class CacheService
{
    const CACHE_PREFIX = 'message:';

    const CACHE_TTL = 86400;

    /**
     * @param string $messageId
     * @param int $queueId
     * @param Carbon $sentAt
     * @return void
     */
    public function storeMessageInfo(string $messageId, int $queueId, Carbon $sentAt): void
    {
        $data = [
            'queue_id' => $queueId,
            'sent_at' => $sentAt->toIso8601String(),
            'cached_at' => now()->toIso8601String()
        ];

        Redis::setex(
            self::CACHE_PREFIX . $messageId,
            self::CACHE_TTL,
            json_encode($data)
        );
    }

    /**
     * @param string $messageId
     * @return array|null
     */
    public function getMessageInfo(string $messageId): ?array
    {
        $data = Redis::get(self::CACHE_PREFIX . $messageId);

        return $data ? json_decode($data, true) : null;
    }
}
