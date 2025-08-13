<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageCollection;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private MessageService  $messageService;

    /**
     * @param MessageService $messageService
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * @param Request $request
     * @return MessageCollection
     */
    public function index(Request $request): MessageCollection
    {
        $perPage = $request->get('per_page', 20);
        $status = $request->get('status', 'sent');

        $messages = $this->messageService->getMessagesByStatus($status, $perPage);

        return new MessageCollection($messages);
    }
}
