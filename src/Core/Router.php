<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;

class Router
{
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

        if (array_key_exists($parseUrl['path'], ROUTES)) {
            $callback = ROUTES[$parseUrl['path']];
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

        $answer = call_user_func_array([$obj, $callback['action']], array_values($params));

        return new Response($answer);
    }

    // public static function ErrorPage404()
    // {
    //     $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    //     header('HTTP/1.1 404 Not Found');
    //     header("Status: 404 Not Found");
    //     header('Location:' . $host . '404');
    // }
}
