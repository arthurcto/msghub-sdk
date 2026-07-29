<?php

namespace MsgHub\Resources;

class TemplateResource extends BaseResource
{
    /** Lista todos os templates do workspace. */
    public function all(): array
    {
        return $this->get('templates');
    }

    /**
     * Cria um novo template.
     * Variáveis no corpo com {nome}, {telefone}, {valor}, etc.
     */
    public function create(string $name, string $body): array
    {
        return $this->post('templates', compact('name', 'body'));
    }

    /** Atualiza um template existente. */
    public function update(int $id, ?string $name = null, ?string $body = null): array
    {
        $payload = array_filter(compact('name', 'body'), fn ($v) => $v !== null);

        return $this->put("templates/{$id}", $payload);
    }

    /** Remove um template. */
    public function remove(int $id): array
    {
        return $this->delete("templates/{$id}");
    }
}
