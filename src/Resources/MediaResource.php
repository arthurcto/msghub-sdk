<?php

namespace MsgHub\Resources;

use GuzzleHttp\Exception\ClientException;
use MsgHub\Exceptions\MsgHubException;

class MediaResource extends BaseResource
{
    /**
     * Faz upload de um arquivo a partir do caminho no disco.
     * Retorna a URL pública permanente do arquivo no MsgHub.
     */
    public function upload(string $filePath): string
    {
        if (! file_exists($filePath)) {
            throw new MsgHubException("Arquivo não encontrado: {$filePath}");
        }

        return $this->uploadMultipart(
            fopen($filePath, 'r'),
            basename($filePath),
        );
    }

    /**
     * Faz upload a partir de um conteúdo em memória (string ou stream).
     * Útil para PDFs gerados dinamicamente, etc.
     */
    public function uploadContents(mixed $contents, string $filename, string $mimeType = 'application/octet-stream'): string
    {
        return $this->uploadMultipart($contents, $filename, $mimeType);
    }

    private function uploadMultipart(mixed $contents, string $filename, ?string $mimeType = null): string
    {
        try {
            $part = ['name' => 'file', 'contents' => $contents, 'filename' => $filename];

            if ($mimeType) {
                $part['headers'] = ['Content-Type' => $mimeType];
            }

            $response = $this->http->request('POST', 'media/upload', [
                'multipart' => [$part],
            ]);

            $data = json_decode((string) $response->getBody(), true) ?? [];

            if (empty($data['url'])) {
                throw new MsgHubException('Upload concluído mas URL não retornada.');
            }

            return $data['url'];
        } catch (ClientException $e) {
            $body = json_decode((string) $e->getResponse()->getBody(), true) ?? [];
            throw new MsgHubException(
                $body['message'] ?? $e->getMessage(),
                $e->getResponse()->getStatusCode(),
                $body,
            );
        } catch (MsgHubException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new MsgHubException($e->getMessage());
        }
    }
}
