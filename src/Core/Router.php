<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;

class Router
{
    const ROUTES = [
        "/user/list" => [
            'method' => 'GET',
            'controller' => 'UserController',
            'action' => 'getUserList',
        ],
        "/user/get/{id}" => [
            'method' => 'GET',
            'controller' => 'UserController',
            'action' => 'getUser',
        ],
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

    private static function parseUrl(Request $request): array
    {
        $parseUrl = parse_url($request->getRoute());
        $url = $parseUrl['path'];

        if (ctype_digit(substr($url, strripos($url, '/') + 1))) {
            $parseUrl = [
                'query' => 'id=' . substr($url, strripos($url, '/', -1) + 1),
            ];
            $parseUrl['path'] = substr($url, 0, strripos($url, '/', -1) + 1) . '{id}';
        }
        return $parseUrl;
    }

    public static function processRequest(Request $request): Response
    {
        $parseUrl = self::parseUrl($request);

        $callback = null;

        if (array_key_exists($parseUrl['path'], self::ROUTES)) {
            $callback = self::ROUTES[$parseUrl['path']];
        }

        // if ($callback['method'] != $method) {
        //     Router::ErrorPage404();
        //     return;
        // }

        $params = [];
        if (array_key_exists('query', $parseUrl)) {
            $query = explode('=', $parseUrl['query']);
            $params[$query[0]] = $query[1];
        }
        foreach ($params as $key => $value) {
            if (ctype_digit($value)) {
                $params[$key] = (int) $value;
            }
        }

        $className = App::getService($callback['controller']);
        $obj = new $className;

        // $answer = call_user_func([$obj, $callback['action']]);

        $answer = call_user_func_array([$obj, $callback['action']], array_values($params));

        return new Response($answer);
    }

    // public static function execute(string $url, string $method)
    // {
    //     $parseUrl = parse_url($url);
    //     var_dump($parseUrl);

    //     $callback = null;

    //     if (array_key_exists($parseUrl['path'], self::ROUTES)) {
    //         $callback = self::ROUTES[$parseUrl['path']];
    //     }
    //     var_dump($callback);
    //     // if ($callback['method'] != $method) {
    //     //     Router::ErrorPage404();
    //     //     return;
    //     // }

    //     $params = [];
    //     if (array_key_exists('query', $parseUrl)) {
    //         $params = explode('=', $parseUrl['query']);
    //     }

    //     var_dump($params);

    //     return call_user_func_array(App::getService($callback['action']), array_values($params));
    // }

    // public static function ErrorPage404()
    // {
    //     $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    //     header('HTTP/1.1 404 Not Found');
    //     header("Status: 404 Not Found");
    //     header('Location:' . $host . '404');
    // }
}
