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

    /**
     * обработка URL
     *
     * @param Request $request
     * @return array
     */
    private static function parseUrl(Request $request): array
    {
        $parseUrl = parse_url($request->getRoute());
        // var_dump($parseUrl);

        $url = explode('/', $parseUrl['path']);

        if (ctype_digit($url[count($url) - 1]) && ctype_digit($url[count($url) - 2])) {
            $parseUrl = [
                'query' => 'id=' . $url[count($url) - 2] . '&user_id=' . $url[count($url) - 1],
            ];
            $parseUrl['path'] = implode('/', array_slice($url, 0, count($url) - 2)) . '/{id}/{user_id}';
            return $parseUrl;
        }

        if (ctype_digit($url[count($url) - 1])) {
            $parseUrl = [
                'query' => 'id=' . $url[count($url) - 1],
            ];
            $parseUrl['path'] = implode('/', array_slice($url, 0, count($url) - 1)) . '/{id}';
            return $parseUrl;
        }

        if (filter_var($url[count($url) - 1], FILTER_VALIDATE_EMAIL)) {
            $parseUrl = [
                'query' =>  'email=' . $url[count($url) - 1],
            ];
            $parseUrl['path'] = implode('/', array_slice($url, 0, count($url) - 1)) . '/{email}';
            return $parseUrl;
        }
        // var_dump($parseUrl);
        // die();
        return $parseUrl;
    }

    /**
     * обработка запроса
     *
     * @param Request $request
     * @return Response
     */
    public static function processRequest(Request $request): Response
    {
        // var_dump($request);
        // die();
        $parseUrl = self::parseUrl($request);
        // var_dump($parseUrl);

        $callback = null;

        if (array_key_exists($parseUrl['path'], ROUTES)) {
            $callback = ROUTES[$parseUrl['path']];
        }
        // var_dump($callback);
        // die();

        // if ($callback['method'] != $method) {
        //     Router::ErrorPage404();
        //     return;
        // }

        $params = !is_null($request->getData()) ? $request->getData() : [];

        if (array_key_exists('query', $parseUrl)) {
            $exploded = explode('&', $parseUrl['query']);
            foreach ($exploded as $pair) {
                $item = explode('=', $pair);
                if (count($item) == 2) {
                    $params[urldecode($item[0])] = urldecode($item[1]);
                }
            }
            foreach ($params as $key => $value) {
                if (ctype_digit($value)) {
                    $params[$key] = (int) $value;
                }
            }
        }
        // var_dump($params);
        // die();

        $className = App::getService($callback['controller']);
        // var_dump($className);
        // die();
        $obj = new $className;

        $answer = call_user_func([$obj, $callback['action']], $params);
        // var_dump($answer);

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
