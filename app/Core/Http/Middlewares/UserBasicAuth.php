<?php

namespace App\Core\Http\Middlewares;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Model\Service\UserService;
use App\Model\DTO\UserDTO;

class UserBasicAuth
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    private function getBasicAuthUser()
    {
        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        $userDTO = $this->userService->getUserByEmail($_SERVER['PHP_AUTH_USER']);

        if (!$userDTO instanceof UserDTO) {
            return false;
        }

        return password_verify($_SERVER['PHP_AUTH_PW'], $userDTO->senha) ? $userDTO : false;
    }

    private function basicAuth($request)
    {
        if ($user = $this->getBasicAuthUser()) {
            $request->user = $user;
            return true;
        }

        throw new \Exception("Usuário ou senha inválidos", 403);
    }

    public function handle($request, $next)
    {
        $this->basicAuth($request);

        return $next($request);
    }
}
