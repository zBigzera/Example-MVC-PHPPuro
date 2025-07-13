<?php

namespace App\Controller\Admin;

use \App\Core\View;
class Alert{
   public static function getError($message){

        return View::render('admin/components/alert.twig',[
            'tipo' => 'danger',
            'mensagem' => $message
        ]);
    }
    public static function getSuccess($message){
        return View::render('admin/components/alert.twig',[
            'tipo' => 'success',
            'mensagem' => $message
        ]);
    }
}