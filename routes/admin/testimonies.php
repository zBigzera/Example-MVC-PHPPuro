<?php

use \App\Core\Http\Response;
use \App\Controller\Admin\Testimony;

$obRouter->group(["require-admin-login"], function($obRouter){

    $obRouter->get("/admin/testimonies", [
        Testimony::class, "getTestimonies"
    ]);

    $obRouter->get("/admin/testimonies/new", [
        Testimony::class, "getNewTestimony"
    ]);

    $obRouter->post("/admin/testimonies/new", [
        Testimony::class, "setNewTestimony"
    ]);

    $obRouter->get("/admin/testimonies/{id}/edit", [
        Testimony::class, "getEditTestimony"
    ]);

    $obRouter->post("/admin/testimonies/{id}/edit", [
        Testimony::class, "setEditTestimony"
    ]);

    $obRouter->get("/admin/testimonies/{id}/delete", [
        Testimony::class, "getDeleteTestimony"
    ]);

    $obRouter->post("/admin/testimonies/{id}/delete", [
        Testimony::class, "setDeleteTestimony"
    ]);

});


