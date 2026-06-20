<?php

namespace MsgHub;

use GuzzleHttp\Client;
use MsgHub\Resources\ConnectionResource;
use MsgHub\Resources\ContactResource;
use MsgHub\Resources\MediaResource;
use MsgHub\Resources\MessageResource;
use MsgHub\Resources\TemplateResource;

class MsgHubClient
{
    private Client $http;

    private ConnectionResource $connectionResource;
    private MessageResource    $messageResource;
    private MediaResource      $mediaResource;
    private ContactResource    $contactResource;
    private TemplateResource   $templateResource;

    public function __construct(string $baseUrl, string $apiKey, int $timeout = 30)
    {
        $this->http = new Client([
            'base_uri' => rtrim($baseUrl, '/') . '/api/v1/',
            'headers'  => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept'        => 'application/json',
            ],
            'timeout'  => $timeout,
        ]);

        $this->connectionResource = new ConnectionResource($this->http);
        $this->messageResource    = new MessageResource($this->http);
        $this->mediaResource      = new MediaResource($this->http);
        $this->contactResource    = new ContactResource($this->http);
        $this->templateResource   = new TemplateResource($this->http);
    }

    // -------------------------------------------------------------------------
    // Atalhos para o uso mais comum
    // -------------------------------------------------------------------------

    /**
     * Envia uma mensagem de texto ou mídia para um número.
     *
     * @param  string      $to        Número no formato 5514999999999
     * @param  string      $text      Texto ou caption
     * @param  string|null $mediaUrl  URL pública do arquivo (retornada por upload())
     */
    public function send(string $to, string $text = '', ?string $mediaUrl = null): array
    {
        return $this->messageResource->send($to, $text, $mediaUrl);
    }

    /**
     * Faz upload de um arquivo do disco e retorna a URL pública permanente.
     * Use a URL retornada em send() ou em múltiplos envios.
     */
    public function upload(string $filePath): string
    {
        return $this->mediaResource->upload($filePath);
    }

    /**
     * Faz upload de um conteúdo em memória (ex: PDF gerado dinamicamente).
     */
    public function uploadContents(mixed $contents, string $filename, string $mimeType = 'application/octet-stream'): string
    {
        return $this->mediaResource->uploadContents($contents, $filename, $mimeType);
    }

    // -------------------------------------------------------------------------
    // Acesso aos recursos completos
    // -------------------------------------------------------------------------

    public function connection(): ConnectionResource
    {
        return $this->connectionResource;
    }

    public function messages(): MessageResource
    {
        return $this->messageResource;
    }

    public function media(): MediaResource
    {
        return $this->mediaResource;
    }

    public function contacts(): ContactResource
    {
        return $this->contactResource;
    }

    public function templates(): TemplateResource
    {
        return $this->templateResource;
    }
}
