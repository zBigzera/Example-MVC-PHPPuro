<?php

use \App\Core\Http\Response;
use \App\Controller\Admin;
$obRouter->get('/admin',
    function($request){
        return new Response(200, Admin\Home::getHome($request));
    }
)->middleware(['require-admin-login']);


