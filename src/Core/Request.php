<?php

declare(strict_types=1);

namespace App\Core;


class Request
{
    private array $data;

    private string $route;

    private string $method;

    public function __construct()
    {
        $this->setRoute();
        $this->setMethod();
        $this->setData();
    }

    public function setMethod(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setRoute(): void
    {
        $this->route = $_SERVER['REQUEST_URI'];
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setData(): void
    {
        $data = [];
        switch ($this->method) {
            case 'GET':
                $data = $_GET;
                break;
            case 'POST':
                $data = $_POST;
                break;
            case 'PUT';
            case 'PATCH';
            case 'DELETE';
                // PUT, PATCH или DELETE
                // $exploded = explode('&', file_get_contents('php://input'));

                // foreach($exploded as $pair) {
                //     $item = explode('=', $pair);
                //     if (count($item) == 2) {
                //         $data[urldecode($item[0])] = urldecode($item[1]);
                //     }
                // }
                break;
            default:
                # code...
                break;
        }

        $this->data = $data;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
