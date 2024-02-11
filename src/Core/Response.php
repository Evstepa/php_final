<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public string $data;
    // public function __construct()
    // {
    // }

    public function setData(array $data)
    {
        $this->data = json_encode($data);
    }

    public function setHeaders()
    {
        header('Content-type: application/json; charset=utf-8');
    }
}
