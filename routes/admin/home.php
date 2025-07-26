<?php

use \App\Core\Http\Response;
use \App\Controller\Admin\Home;

$obRouter->get("/admin", [
    Home::class, "getHome"
])->middleware(["require-admin-login"]);


