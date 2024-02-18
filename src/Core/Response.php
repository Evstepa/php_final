<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public const STATUS = [
        '200' => '200 OK',
        '404' => 'HTTP/1.0 404 Not Found',
    ];
    public string $data;
    public array $headers;

    public function __construct($data)
    {
        $this->setData($data);
    }

    public function setData($data)
    {
        if ($data) {
            $this->setHeaders('200');
        } else {
            $this->setHeaders('404');
        }
        $this->data = $data;
        // $this->data = json_encode($data);
    }

    public function setHeaders(string $status)
    {
        $this->headers[] = 'Content-type: application/json; charset=utf-8';
        $this->headers[] = self::STATUS[$status];
    }
}
