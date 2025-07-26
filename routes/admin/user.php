<?php

use \App\Core\Http\Response;
use \App\Controller\Admin\User;

$obRouter->group(["require-admin-login"], function($obRouter){

    $obRouter->get("/admin/users", [
        User::class, "getUsers"
    ]);

    $obRouter->get("/admin/users/new", [
        User::class, "getNewUser"
    ]);

    $obRouter->post("/admin/users/new", [
        User::class, "setNewUser"
    ]);

    $obRouter->get("/admin/users/{id}/edit", [
        User::class, "getEditUser"
    ]);

    $obRouter->post("/admin/users/{id}/edit", [
        User::class, "setEditUser"
    ]);

    $obRouter->get("/admin/users/{id}/delete", [
        User::class, "getDeleteUser"
    ]);

    $obRouter->post("/admin/users/{id}/delete", [
        User::class, "setDeleteUser"
    ]);

});


