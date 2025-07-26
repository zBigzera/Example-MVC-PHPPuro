<?php

use App\Controller\Api\Testimony;

// Rotas públicas com cache
$obRouter->group(['api', 'cache'], function($obRouter) {
    $obRouter->get('/api/v1/testimonies', [
        Testimony::class, 'getTestimonies'
    ]);

    $obRouter->get('/api/v1/testimonies/{id}/', [
        Testimony::class, 'getTestimony'
    ]);
});

// Rotas protegidas com autenticação básica
$obRouter->group(['api', 'user-basic-auth'], function($obRouter) {
    $obRouter->post('/api/v1/testimonies/', [
        Testimony::class, 'setNewTestimony'
    ]);

    $obRouter->put('/api/v1/testimonies/{id}', [
        Testimony::class, 'setEditTestimony'
    ]);

    $obRouter->delete('/api/v1/testimonies/{id}', [
        Testimony::class, 'setDeleteTestimony'
    ]);
});
