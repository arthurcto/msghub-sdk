<?php

namespace MsgHub\Exceptions;

use RuntimeException;

class MsgHubException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        private readonly array $body = [],
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}
