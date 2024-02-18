<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\Request;

require_once 'vendor/autoload.php';
require_once 'autoload.php';

$request = new Request();

// echo "<pre>";
// var_dump($request->getRoute(), $request->getMethod(), $request->getData());
// echo "</pre>";

$response = Router::processRequest($request);
foreach ($response->headers as $item) {
    header($item);
}
echo $response->data;
