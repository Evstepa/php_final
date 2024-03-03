<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\Request;

require_once 'vendor/autoload.php';

require_once('./src/Config/routes.php');
require_once('./src/Config/const.php');
require_once('./src/Config/textMessages.php');
// require_once 'autoload.php';

$request = new Request();
// var_dump($request);

if ($request->getRoute() === '/') {
    echo 'Hello!';
    return;
}
$response = Router::processRequest($request);
foreach ($response->headers as $item) {
    header($item);
}
echo $response->data;
