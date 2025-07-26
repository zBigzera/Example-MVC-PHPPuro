<?php
use \App\Controller\Api\Auth;

$obRouter->post('/api/v1/auth', [
    Auth::class, 'generateToken'
])->middleware(['api']);
