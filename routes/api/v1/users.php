<?php
use App\Controller\Api\User;

// Rotas protegidas com JWT
$obRouter->group(['api', 'jwt-auth'], function($obRouter) {
    $obRouter->get('/api/v1/users/me', [
        User::class, 'getCurrentUser'
    ]);

    $obRouter->post('/api/v1/users/', [
        User::class, 'setNewUser'
    ]);

    $obRouter->put('/api/v1/users/{id}', [
        User::class, 'setEditUser'
    ]);

    $obRouter->delete('/api/v1/users/{id}', [
        User::class, 'setDeleteUser'
    ]);
});

// Rotas públicas com cache
$obRouter->group(['api', 'cache'], function($obRouter) {
    $obRouter->get('/api/v1/users', [
        User::class, 'getUsers'
    ]);

    $obRouter->get('/api/v1/users/{id}/', [
        User::class, 'getUser'
    ]);
});