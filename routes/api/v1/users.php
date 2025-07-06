<?php

use \App\Http\Response;
use \App\Controller\Api;

//listar usuarios
$obRouter->get('/api/v1/users', [
    'middlewares' => [
        'api'
    ],

    function($request){
        return new Response (200, Api\User::getUsers($request), 'application/json');
    }
]);

//consulta do usuário atual
$obRouter->get('/api/v1/users/me', [
    'middlewares' => [
        'api',
        'jwt-auth'
    ],

    function($request){
   return new Response (200, Api\User::getCurrentUser($request), 'application/json');
    }
]);


$obRouter->get('/api/v1/users/{id}/', [
     'middlewares' => [
        'api'
    ],
    function($request, $id){
        return new Response (200, Api\User::getUser($request, (int)$id), 'application/json');
    }
]);

$obRouter->post('/api/v1/users/', [
     'middlewares' => [
        'api',
       'jwt-auth'
    ],
    function($request){
        return new Response (201, Api\User::setNewUser($request), 'application/json');
    }
]);


$obRouter->put('/api/v1/users/{id}', [
     'middlewares' => [
        'api',
       'jwt-auth'
    ],
    function($request, $id){
        return new Response (200, Api\User::setEditUser($request, $id), 'application/json');
    }
]);


$obRouter->delete('/api/v1/users/{id}', [
     'middlewares' => [
        'api',
       'jwt-auth'
    ],
    function($request, $id){
        return new Response (200, Api\User::setDeleteUser($request, $id), 'application/json');
    }
]);
