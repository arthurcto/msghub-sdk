<?php

namespace MsgHub\Resources;

class MessageResource extends BaseResource
{
    /** Lista mensagens paginadas. */
    public function all(int $page = 1): array
    {
        return $this->get('messages', ['page' => $page]);
    }

    /**
     * Envia uma mensagem de texto ou mídia.
     *
     * @param  string      $to        Número no formato 5514999999999
     * @param  string      $text      Texto ou caption da mídia
     * @param  string|null $mediaUrl  URL pública do arquivo (imagem, vídeo, áudio, documento)
     */
    public function send(string $to, string $text = '', ?string $mediaUrl = null): array
    {
        $payload = ['to' => $to];

        if ($text !== '') {
            $payload['text'] = $text;
        }

        if ($mediaUrl) {
            $payload['media_url'] = $mediaUrl;
        }

        return $this->post('messages/send', $payload);
    }
}
