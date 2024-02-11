<?php

declare(strict_types=1);

namespace App\Core;


class Request
{
    // private array $data;

    // private string $route;

    // private string $method;

    // public function __construct()
    // {
    //     $this->route = self::getRoute();
    //     $this->method = self::getMethod();
    //     $this->data = self::getData();
    // }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getRoute(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getData(): array
    {
        if (self::getMethod() === 'GET') return $_GET;
        if (self::getMethod() === 'POST') return $_POST;

        // PUT, PATCH или DELETE
        $data = [];
        // $exploded = explode('&', file_get_contents('php://input'));

        // foreach($exploded as $pair) {
        //     $item = explode('=', $pair);
        //     if (count($item) == 2) {
        //         $data[urldecode($item[0])] = urldecode($item[1]);
        //     }
        // }

        return $data;
    }
}
