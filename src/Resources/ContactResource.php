<?php

namespace MsgHub\Resources;

class ContactResource extends BaseResource
{
    /**
     * Lista contatos do workspace.
     *
     * @param  string|null $search  Busca por nome ou telefone
     * @param  string|null $tag     Filtra por tag
     */
    public function all(?string $search = null, ?string $tag = null): array
    {
        $query = array_filter(compact('search', 'tag'));

        return $this->get('contacts', $query);
    }

    /**
     * Cria um novo contato.
     *
     * @param  string   $phone  Número no formato 5514999999999
     * @param  string   $name   Nome do contato
     * @param  string[] $tags   Tags para segmentação
     */
    public function create(string $phone, string $name, array $tags = []): array
    {
        return $this->post('contacts', compact('phone', 'name', 'tags'));
    }

    /**
     * Atualiza um contato existente.
     *
     * @param  int          $id
     * @param  string|null  $phone
     * @param  string|null  $name
     * @param  string[]     $tags
     */
    public function update(int $id, ?string $phone = null, ?string $name = null, ?array $tags = null): array
    {
        $payload = array_filter([
            'phone' => $phone,
            'name'  => $name,
            'tags'  => $tags,
        ], fn ($v) => $v !== null);

        return $this->put("contacts/{$id}", $payload);
    }

    /** Remove um contato. */
    public function remove(int $id): array
    {
        return $this->delete("contacts/{$id}");
    }
}
