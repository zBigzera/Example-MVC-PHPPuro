<?php
$container = require __DIR__."/bootstrap/app.php";
use App\Core\Http\Router;
$obRouter = new Router(URL, $container);

includeAll(__DIR__ . '/routes/', $obRouter);

$obRouter->run()->sendResponse();
