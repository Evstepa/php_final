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

    /**
     * Установить метода запроса
     *
     * @return void
     */
    public function setMethod(): void
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Вернуть метод запроса
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Установить маршрут запроса
     *
     * @return void
     */
    public function setRoute(): void
    {
        $this->route = $_SERVER['REQUEST_URI'];
    }

    /**
     * Вернуть маршрут запроса
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Установить передаваемые в запросе данные
     *
     * @return void
     */
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
            case 'DELETE':
                $exploded = explode('&', file_get_contents('php://input'));
                foreach ($exploded as $pair) {
                    $item = explode('=', $pair);
                    if (count($item) == 2) {
                        $data[urldecode($item[0])] = urldecode($item[1]);
                    }
                }
                break;
            default:
                break;
        }
        $data['token'] = $this->getAuthToken();
        $this->data = $data;
    }

    /**
     * Вернуть передаваемые в запросе данные
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Вернуть передаваемый в запросе токен
     *
     * @return string
     */
    public function getAuthToken(): string
    {
        if (!isset(getallheaders()["Authorization"])) {
            return '';
        }
        $authToken = getallheaders()["Authorization"];
        return trim(str_ireplace('Bearer ', '', $authToken));
    }
}
