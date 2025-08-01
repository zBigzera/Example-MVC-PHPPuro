<?php

namespace App\Controller\Admin;

class Home extends Page {
    public static function getHome($request) {
        return parent::render('admin/pages/index.twig', [
            'title' => 'Home admin'
        ], 'home');
    }
}
