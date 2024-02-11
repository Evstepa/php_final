<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    // public function __construct()
    // {
    // }

    public function setData(array $data): string
    {
        return json_encode($data);
    }

    public function setHeaders()
    {
    }
}
