<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public string $data;
    public array $headers;

    public function __construct(string $data)
    {
        $this->setData($data);
    }

    public function setData(string $data): void
    {
        if ($data) {
            $this->setHeaders('200');
        } else {
            $this->setHeaders('404');
        }
        $this->data = $data;
    }

    public function setHeaders(string $status)
    {
        $this->headers[] = 'Content-type: application/json; charset=utf-8';
        $this->headers[] = STATUS_MESSAGES[$status];
    }
}
