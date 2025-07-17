<?php

use \App\Core\Http\Response;
use \App\Controller\Api;

$obRouter->group(['api', 'cache'], function($obRouter) {
    $obRouter->get('/api/v1/users', [
        function($request){
            return new Response(200, Api\User::getUsers($request), 'application/json');
        }
    ]);

    $obRouter->get('/api/v1/users/{id}/', [
        function($request, $id){
            return new Response(200, Api\User::getUser($request, (int)$id), 'application/json');
        }
    ]);
});

$obRouter->group(['api', 'jwt-auth'], function($obRouter) {
    $obRouter->get('/api/v1/users/me', [
        function($request){
            return new Response(200, Api\User::getCurrentUser($request), 'application/json');
        }
    ]);

    $obRouter->post('/api/v1/users/', [
        function($request){
            return new Response(201, Api\User::setNewUser($request), 'application/json');
        }
    ]);

    $obRouter->put('/api/v1/users/{id}', [
        function($request, $id){
            return new Response(200, Api\User::setEditUser($request, $id), 'application/json');
        }
    ]);

    $obRouter->delete('/api/v1/users/{id}', [
        function($request, $id){
            return new Response(200, Api\User::setDeleteUser($request, $id), 'application/json');
        }
    ]);
});
