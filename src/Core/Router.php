<?php

declare(strict_types=1);

namespace App\Core;

use Exception;
use App\Core\Request;
use App\Core\Response;

class Router
{
    const ROUTES = [
        "/user/list" => [
            'method' => 'GET',
            'action' => 'UserController::getUsetList',
        ],
        "/user/get/{id}" => "",
        "/user/update" => "",
        "/user/login" => "",
        "/user/logout" => "",
        "/user/reset_password" => "",
    ];

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    public function processRequest(Request $request): Response
    {
        return new Response();
    }

    public static function execute(string $url, string $method)
    {
        $parseUrl = parse_url($url);
        var_dump($parseUrl);

        $callback = null;

        if (array_key_exists($parseUrl['path'], self::ROUTES)) {
            $callback = self::ROUTES[$parseUrl['path']];
        }
        var_dump($callback);
        // if ($callback['method'] != $method) {
        //     Router::ErrorPage404();
        //     return;
        // }

        $params = [];
        if (array_key_exists('query', $parseUrl)) {
            $params = explode('=', $parseUrl['query']);
        }

        var_dump($params);

        // return call_user_func_array($callback['action'], array_values($params));
    }

    public static function ErrorPage404()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:' . $host . '404');
    }
}
