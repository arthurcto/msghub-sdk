<?php

namespace MsgHub\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use MsgHub\MsgHubClient;
use MsgHub\Resources\ConnectionResource;
use MsgHub\Resources\ContactResource;
use MsgHub\Resources\MediaResource;
use MsgHub\Resources\MessageResource;
use MsgHub\Resources\TemplateResource;

/**
 * @method static array  send(string $to, string $text = '', ?string $mediaUrl = null)
 * @method static string upload(string $filePath)
 * @method static string uploadContents(mixed $contents, string $filename, string $mimeType = 'application/octet-stream')
 * @method static ConnectionResource connection()
 * @method static MessageResource    messages()
 * @method static MediaResource      media()
 * @method static ContactResource    contacts()
 * @method static TemplateResource   templates()
 */
class MsgHub extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MsgHubClient::class;
    }
}
