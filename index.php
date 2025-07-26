<?php
$container = require __DIR__."/bootstrap/app.php";
use App\Core\Http\Router;
$obRouter = new Router(URL, $container);

include __DIR__ . "/routes/routes.php";

$obRouter->run()->sendResponse();
