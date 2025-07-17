<?php

use \App\Core\Http\Response;
use \App\Controller\Admin;

$obRouter->group(['require-admin-login'], function($obRouter){

    $obRouter->get('/admin/users', [
        function($request){
            return new Response(200, Admin\User::getUsers($request));
        }
    ]);

    $obRouter->get('/admin/users/new', [
        function($request){
            return new Response(200, Admin\User::getNewUser($request));
        }
    ]);

    $obRouter->post('/admin/users/new', [
        function($request){
            return new Response(200, Admin\User::setNewUser($request));
        }
    ]);

    $obRouter->get('/admin/users/{id}/edit', [
        function($request, $id){
            return new Response(200, Admin\User::getEditUser($request, $id));
        }
    ]);

    $obRouter->post('/admin/users/{id}/edit', [
        function($request, $id){
            return new Response(200, Admin\User::setEditUser($request, $id));
        }
    ]);

    $obRouter->get('/admin/users/{id}/delete', [
        function($request, $id){
            return new Response(200, Admin\User::getDeleteUser($request, $id));
        }
    ]);

    $obRouter->post('/admin/users/{id}/delete', [
        function($request, $id){
            return new Response(200, Admin\User::setDeleteUser($request, $id));
        }
    ]);

});
