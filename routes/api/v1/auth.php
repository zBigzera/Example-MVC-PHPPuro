<?php

use \App\Core\Http\Response;
use \App\Controller\Api;


$obRouter->post('/api/v1/auth', [
    function($request){
        return new Response (201, Api\Auth::generateToken($request), 'application/json');
    }
])->middleware(['api']);


