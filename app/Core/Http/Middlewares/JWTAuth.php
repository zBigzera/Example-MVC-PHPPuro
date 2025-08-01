<?php

namespace App\Core\Http\Middlewares;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Model\Service\UserService;
use App\Model\DTO\UserDTO;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    private function getJWTAuthUser(Request $request)
    {
        $headers = $request->getHeaders();
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        try {
            $decoded = JWT::decode($jwt, new Key(getenv('JWT_KEY'), 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception("Token inválido", 403);
        }

        $email = $decoded->email ?? '';

        $userDTO = $this->userService->getUserByEmail($email);

        return $userDTO instanceof UserDTO ? $userDTO : false;
    }

    private function auth(Request $request)
    {
        if ($user = $this->getJWTAuthUser($request)) {
            $request->user = $user;
            return true;
        }

        throw new \Exception("Acesso negado", 403);
    }

    public function handle(Request $request, $next)
    {
        $this->auth($request);
        return $next($request);
    }
}
