<?php
use App\Controller\Api\Api;

$obRouter->get('/api/v1', [
    Api::class, 'getDetails'
])->middleware(['api']);
