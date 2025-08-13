<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageCollection;
use App\Services\MessageService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="SMS API",
 *     version="1.0.0",
 *     description="SMS gönderim servisi API dokümantasyonu"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="apiKey",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-Key"
 * )
 *
 * @OA\Tag(
 *     name="Messages",
 *     description="Mesaj işlemleri"
 * )
 */
class MessageController extends Controller
{
    /**
     * @var MessageService
     */
    private MessageService  $messageService;

    /**
     * @param MessageService $messageService
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * @OA\Get(
     *     path="/messages",
     *     summary="Gönderilen mesajları listele",
     *     description="Belirtilen duruma göre mesajları sayfalı olarak listeler",
     *     operationId="getMessages",
     *     tags={"Messages"},
     *     security={{"apiKey": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Mesaj durumu (sent, failed)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"sent", "failed"},
     *             default="sent"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Sayfa başına kayıt sayısı",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20,
     *             minimum=1,
     *             maximum=100
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Sayfa numarası",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1,
     *             minimum=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Başarılı",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="phone_number", type="string", example="+905551234567"),
     *                     @OA\Property(property="content", type="string", example="Mesaj içeriği"),
     *                     @OA\Property(property="status", type="string", example="sent"),
     *                     @OA\Property(property="message_id", type="integer", example=1),
     *                     @OA\Property(property="sent_at", type="string", format="date-time", example="2025-08-13 10:00:00"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-13 10:00:00")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=20),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/messages")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost/api/messages?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost/api/messages?page=3"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", example="http://localhost/api/messages?page=2")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Yetkisiz erişim",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request): MessageCollection
    {
        $perPage = $request->get('per_page', 20);
        $status = $request->get('status', 'sent');

        $messages = $this->messageService->getMessagesByStatus($status, $perPage);

        return new MessageCollection($messages);
    }
}
