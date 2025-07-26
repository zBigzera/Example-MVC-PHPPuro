<?php

use \App\Core\Http\Response;
use \App\Controller\Admin\Login;

$obRouter->group(["require-admin-logout"], function($obRouter){

$obRouter->get("/admin/login",[
    Login::class, "getLogin"
]);

$obRouter->post("/admin/login",[
    Login::class, "setLogin"
]);
});


$obRouter->get("/admin/logout",[
    Login::class, "setLogout"
])->middleware(["require-admin-login"]);


