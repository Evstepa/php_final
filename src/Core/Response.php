<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public string $body;
    public array $headers;
    public int $status;

    public function __construct(array $data)
    {
        $this->setData($data);
    }

    /**
     * Установить данные ответа
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->body = json_encode($data['body']);
        $this->status = $data['status'];
        $this->headers[] = 'Content-type: application/json; charset=utf-8';
        $this->headers[] = STATUS_MESSAGES[$this->status];
    }

    /**
     * Отправить ответ
     *
     * @return void
     */
    public function send(): void
    {
        foreach ($this->headers as $item) {
            header($item);
        }
        http_response_code($this->status);
        echo $this->body;
    }
}
