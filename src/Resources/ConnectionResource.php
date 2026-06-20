<?php

namespace MsgHub\Resources;

class ConnectionResource extends BaseResource
{
    /** Retorna status da conexão WhatsApp. */
    public function status(): array
    {
        return $this->get('connection');
    }

    /**
     * Cria uma nova conexão (instância Evolution).
     * @param  string|null $instanceName  Nome da instância; gerado automaticamente se omitido.
     */
    public function create(?string $instanceName = null): array
    {
        $payload = ['provider' => 'evolution'];

        if ($instanceName) {
            $payload['instance_name'] = $instanceName;
        }

        return $this->post('connection', $payload);
    }

    /** Remove a conexão atual. */
    public function delete(): array
    {
        return $this->delete('connection');
    }

    /** Retorna o QR code (base64) para escanear no WhatsApp. */
    public function qrCode(): array
    {
        return $this->get('connection/qrcode');
    }

    /** Atalho: retorna true se a conexão está ativa. */
    public function isConnected(): bool
    {
        return ($this->status()['status'] ?? null) === 'connected';
    }
}
