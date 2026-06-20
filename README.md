# MsgHub SDK

Biblioteca PHP oficial para integração com o **MsgHub** — gateway WhatsApp com suporte a mensagens, mídia, contatos e templates.

---

## Instalação

### Via repositório Git privado

Adicione ao `composer.json` do seu projeto:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:sua-org/msghub-sdk.git"
        }
    ],
    "require": {
        "msghub/sdk": "^1.0"
    }
}
```

```bash
composer install
```

### Dependência obrigatória

```bash
composer require guzzlehttp/guzzle
```

---

## Configuração

### Laravel (recomendado)

O Service Provider é registrado automaticamente via auto-discovery.

Adicione ao `.env`:

```env
MSGHUB_URL=http://msghub.arthurti.com.br
MSGHUB_API_KEY=mk_xxxxxxxxxxxxxxxxxxxxxxxx
MSGHUB_TIMEOUT=30
```

Publique o arquivo de configuração (opcional):

```bash
php artisan vendor:publish --tag=msghub-config
```

### PHP puro (sem Laravel)

```php
use MsgHub\MsgHubClient;

$msghub = new MsgHubClient(
    baseUrl: 'http://msghub.arthurti.com.br',
    apiKey:  'mk_xxxxxxxxxxxxxxxxxxxxxxxx',
);
```

---

## Uso — Laravel (Facade)

```php
use MsgHub\Laravel\Facades\MsgHub;
```

### Uso — PHP puro

```php
use MsgHub\MsgHubClient;

$msghub = new MsgHubClient('http://msghub.arthurti.com.br', 'mk_xxx');
```

> Nos exemplos abaixo, `MsgHub::` equivale a `$msghub->` no PHP puro.

---

## Mensagens

### Enviar texto

```php
MsgHub::send('5514999999999', 'Olá! Como podemos ajudar?');
```

### Enviar mídia por URL pública

```php
MsgHub::send('5514999999999', 'Confira nossa promoção!', mediaUrl: 'https://site.com/promo.jpg');
```

### Enviar mídia sem caption

```php
MsgHub::send('5514999999999', mediaUrl: 'https://site.com/cardapio.pdf');
```

---

## Upload de arquivos

### Fazer upload de um arquivo e obter a URL

```php
$url = MsgHub::upload('/path/to/imagem.jpg');
// → "http://msghub.arthurti.com.br/storage/media/1/2026/06/uuid.jpg"
```

### Reutilizar a URL em múltiplos envios

```php
// Upload único — envio para vários
$url = MsgHub::upload(storage_path('app/parabens.jpg'));

foreach ($aniversariantes as $cliente) {
    MsgHub::send($cliente->whatsapp, "Feliz aniversário, {$cliente->nome}!", mediaUrl: $url);
}
```

### Upload de conteúdo gerado em memória

```php
// PDF gerado com barryvdh/laravel-dompdf ou similar
$pdf = PDF::loadView('boleto', $dados)->output();

$url = MsgHub::uploadContents($pdf, 'boleto.pdf', 'application/pdf');
MsgHub::send($cliente->whatsapp, 'Segue seu boleto de cobrança:', mediaUrl: $url);
```

### Upload de imagem gerada dinamicamente

```php
// Imagem criada com GD ou Imagick
ob_start();
imagepng($imagem);
$conteudo = ob_get_clean();

$url = MsgHub::uploadContents($conteudo, 'grafico.png', 'image/png');
MsgHub::send($gerente->whatsapp, 'Relatório do dia:', mediaUrl: $url);
```

---

## Exemplos práticos

### Aniversariantes do dia (ERP)

```php
$aniversariantes = Cliente::whereMonth('nascimento', now()->month)
    ->whereDay('nascimento', now()->day)
    ->get();

if ($aniversariantes->isEmpty()) {
    return;
}

$url = MsgHub::upload(storage_path('app/parabens.jpg'));

foreach ($aniversariantes as $cliente) {
    MsgHub::send(
        $cliente->whatsapp,
        "Feliz aniversário, {$cliente->nome}! 🎂 A equipe deseja um ótimo dia!",
        mediaUrl: $url,
    );
}
```

### Boleto gerado automaticamente

```php
$boleto  = $financeiro->gerarBoleto($contrato);
$pdf     = $boleto->renderizarPdf();

$url = MsgHub::uploadContents($pdf, "boleto_{$contrato->id}.pdf", 'application/pdf');

MsgHub::send(
    $contrato->cliente->whatsapp,
    "Olá, {$contrato->cliente->nome}! Seu boleto com vencimento em {$boleto->vencimento->format('d/m/Y')} está disponível:",
    mediaUrl: $url,
);
```

### Alerta de promoção para lista segmentada

```php
$clientes = MsgHub::contacts()->all(tag: 'promocao-junho');

$url = MsgHub::upload(storage_path('app/promo-junho.jpg'));

foreach ($clientes['data'] as $contato) {
    MsgHub::send(
        $contato['phone'],
        "Promoção exclusiva para você! Válida até 30/06. Acesse: https://site.com/promo",
        mediaUrl: $url,
    );
    sleep(1); // respeita rate limit do WhatsApp
}
```

### Confirmação de agendamento

```php
MsgHub::send(
    $paciente->whatsapp,
    "Olá, {$paciente->nome}! Confirmamos seu agendamento:\n\n" .
    "📅 Data: {$consulta->data->format('d/m/Y')}\n" .
    "⏰ Hora: {$consulta->hora}\n" .
    "📍 Local: {$consulta->unidade->endereco}\n\n" .
    "Responda SIM para confirmar ou ligue para reagendar.",
);
```

---

## Conexão

```php
// Verificar se está conectado
if (MsgHub::connection()->isConnected()) {
    echo 'WhatsApp conectado!';
}

// Status completo
$status = MsgHub::connection()->status();
// → ['status' => 'connected', 'phone_number' => '5514...', ...]

// QR Code para escanear (base64)
$qr = MsgHub::connection()->qrCode();
// → ['qrcode' => 'data:image/png;base64,...']
```

---

## Contatos

```php
// Listar todos
$contatos = MsgHub::contacts()->all();

// Buscar por nome ou telefone
$contatos = MsgHub::contacts()->all(search: 'João');

// Filtrar por tag
$vips = MsgHub::contacts()->all(tag: 'vip');

// Criar
MsgHub::contacts()->create('5514999999999', 'João Silva', ['cliente', 'vip']);

// Atualizar
MsgHub::contacts()->update($id, name: 'João S. Silva', tags: ['cliente', 'vip', 'premium']);

// Remover
MsgHub::contacts()->delete($id);
```

---

## Templates

```php
// Listar
$templates = MsgHub::templates()->all();

// Criar (variáveis com {nome}, {valor}, etc.)
MsgHub::templates()->create(
    'Boas-vindas',
    'Olá, {nome}! Seja bem-vindo(a). Seu cadastro foi realizado com sucesso.',
);

// Atualizar
MsgHub::templates()->update($id, body: 'Nova mensagem de boas-vindas, {nome}!');

// Remover
MsgHub::templates()->delete($id);
```

---

## Histórico de mensagens

```php
// Primeira página
$mensagens = MsgHub::messages()->all();

// Página específica
$mensagens = MsgHub::messages()->all(page: 2);
```

---

## Tratamento de erros

```php
use MsgHub\Exceptions\MsgHubException;

try {
    MsgHub::send('5514999999999', 'Teste');
} catch (MsgHubException $e) {
    echo $e->getMessage();      // mensagem de erro
    echo $e->getStatusCode();   // HTTP status (400, 401, 422, 500...)
    print_r($e->getBody());     // resposta completa da API
}
```

---

## Referência da API

| Método | Descrição |
|--------|-----------|
| `send($to, $text, $mediaUrl)` | Envia texto ou mídia |
| `upload($filePath)` | Upload de arquivo, retorna URL |
| `uploadContents($content, $filename, $mimeType)` | Upload de conteúdo em memória, retorna URL |
| `connection()->status()` | Status da conexão WhatsApp |
| `connection()->isConnected()` | `true` se conectado |
| `connection()->qrCode()` | QR code em base64 |
| `connection()->create($instanceName)` | Cria nova conexão |
| `connection()->delete()` | Remove conexão |
| `contacts()->all($search, $tag)` | Lista contatos |
| `contacts()->create($phone, $name, $tags)` | Cria contato |
| `contacts()->update($id, $phone, $name, $tags)` | Atualiza contato |
| `contacts()->delete($id)` | Remove contato |
| `templates()->all()` | Lista templates |
| `templates()->create($name, $body)` | Cria template |
| `templates()->update($id, $name, $body)` | Atualiza template |
| `templates()->delete($id)` | Remove template |
| `messages()->all($page)` | Histórico de mensagens |

---

## Tipos de mídia suportados

| Tipo | Extensões |
|------|-----------|
| Imagem | jpg, jpeg, png, gif, webp |
| Vídeo | mp4, mov, avi, webm |
| Áudio | mp3, ogg, opus, m4a |
| Documento | pdf, docx, xlsx, pptx, zip, csv, txt |

> Arquivos enviados via `upload()` ficam armazenados no MsgHub por **90 dias** por padrão (configurável pelo administrador).
