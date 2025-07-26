<?php
use \App\Controller\Site\Home;
use \App\Controller\Site\About;
use \App\Controller\Site\Testimony;

$obRouter->get('/', [Home::class, 'getHome']);

$obRouter->group(['cache'], function($obRouter) {
    $obRouter->get('/sobre', [About::class, 'getAbout']);
    $obRouter->post('/depoimentos', [Testimony::class, 'insertTestimony']);
});

$obRouter->get('/depoimentos', [Testimony::class, 'getTestimonies']);
