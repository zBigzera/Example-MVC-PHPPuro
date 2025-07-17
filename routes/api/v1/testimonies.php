<?php

use \App\Core\Http\Response;
use \App\Controller\Api;

$obRouter->group(['api', 'cache'], function($obRouter) {
    $obRouter->get('/api/v1/testimonies', [
        function($request){
            return new Response(200, Api\Testimony::getTestimonies($request), 'application/json');
        }
    ]);

    $obRouter->get('/api/v1/testimonies/{id}/', [
        function($request, $id){
            return new Response(200, Api\Testimony::getTestimony($request, (int)$id), 'application/json');
        }
    ]);
});

$obRouter->group(['api', 'user-basic-auth'], function($obRouter) {
    $obRouter->post('/api/v1/testimonies/', [
        function($request){
            return new Response(201, Api\Testimony::setNewTestimony($request), 'application/json');
        }
    ]);

    $obRouter->put('/api/v1/testimonies/{id}', [
        function($request, $id){
            return new Response(200, Api\Testimony::setEditTestimony($request, $id), 'application/json');
        }
    ]);

    $obRouter->delete('/api/v1/testimonies/{id}', [
        function($request, $id){
            return new Response(200, Api\Testimony::setDeleteTestimony($request, $id), 'application/json');
        }
    ]);
});
