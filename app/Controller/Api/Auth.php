<?php

namespace App\Controller\Api;

use App\Model\Service\UserService;
use App\Model\DTO\UserDTO;
use Firebase\JWT\JWT;
use App\Core\Http\Request;

class Auth extends Api
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Gera um token JWT
     * @param Request $request
     * @return array
     */
    public function generateToken($request)
    {
        $postVars = $request->getPostVars();

        if (!isset($postVars['email']) || !isset($postVars['senha'])) {
            throw new \Exception("Os campos 'email' e 'senha' são obrigatórios", 400);
        }

        $userDTO = $this->userService->getUserByEmail($postVars['email']);

        if (!$userDTO instanceof UserDTO || !password_verify($postVars['senha'], $userDTO->senha)) {
            throw new \Exception("Usuário ou senha inválidos", 400);
        }

        $payload = [
            'email' => $userDTO->email
        ];

        return [
            'token' => JWT::encode($payload, getenv('JWT_KEY'), 'HS256')
        ];
    }
}
