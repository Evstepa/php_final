<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Обработка URL
     *
     * @param Request $request
     * @return array
     */
    private static function parseUrl(Request $request): array
    {
        $parseUrl = parse_url($request->getRoute());

        $url = explode('/', $parseUrl['path']);

        if (
            ctype_digit($url[count($url) - 1])
            && ctype_digit($url[count($url) - 2])
        ) {
            $parseUrl = [
                'query' => sprintf(
                    "id=%s&user_id=%s",
                    $url[count($url) - 2],
                    $url[count($url) - 1]
                ),
            ];
            $parseUrl['path'] = implode(
                '/',
                array_slice($url, 0, count($url) - 2)
            ) . '/{id}/{user_id}';
            return $parseUrl;
        }

        if (ctype_digit($url[count($url) - 1])) {
            $parseUrl = [
                'query' => 'id=' . $url[count($url) - 1],
            ];
            $parseUrl['path'] = implode(
                '/',
                array_slice($url, 0, count($url) - 1)
            ) . '/{id}';
            return $parseUrl;
        }

        if (filter_var($url[count($url) - 1], FILTER_VALIDATE_EMAIL)) {
            $parseUrl = [
                'query' =>  'email=' . $url[count($url) - 1],
            ];
            $parseUrl['path'] = implode(
                '/',
                array_slice($url, 0, count($url) - 1)
            ) . '/{email}';
            return $parseUrl;
        }

        return $parseUrl;
    }

    /**
     * Обработка запроса
     *
     * @param Request $request
     * @return Response
     */
    public static function processRequest(Request $request): Response
    {
        $parseUrl = self::parseUrl($request);

        $callback = null;

        if (array_key_exists($parseUrl['path'], ROUTES)) {
            $callback = ROUTES[$parseUrl['path']];
        }

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

        $className = App::getService($callback['controller']);

        $obj = new $className;

        $answer = call_user_func([$obj, $callback['action']], $params);

        return new Response($answer);
    }
}
