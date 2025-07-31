<?php

namespace App\Model\DTO;

class TestimonyDTO
{

    public ?int $id = null;

    public string $nome;

    public string $mensagem;

    public string $data;

    public function __construct(?int $id, string $nome, string $mensagem, string $data){

        $this->id = $id;
        $this->nome = $nome;
        $this->mensagem = $mensagem;
        $this->data = $data; 

    }

    public static function fromArray(array $data): TestimonyDTO{
        return new Self(
            $data['id'] ?? null,
            $data['nome'] ?? '',
            $data['mensagem'] ?? '',
            $data['data'] ?? ''
        );
    }

    public function toArray(): array{
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ];
    }
}

