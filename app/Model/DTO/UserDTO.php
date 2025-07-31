<?php

namespace App\Model\DTO;


class UserDTO
{
    public ?int $id = null;
    public string $nome;

    public string $email;

    public string $senha;

    public function __construct(?int $id = null, ?string $nome = null, ?string $email = null, ?string $senha = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ];
    }

    public static function fromArray(array $data): self
    {

        return new self(
            $data['id'] ?? null,
            $data['nome'] ?? '',
            $data['email'] ?? '',
            $data['senha'] ?? ''
        );
    }

}