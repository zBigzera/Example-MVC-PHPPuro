<?php

namespace App\Model\DTO;

class OrganizationDTO
{
    public int $id = 1;
    public string $name = "Otávio Bigogno";   

    public string $description = "otaviobigogno37@gmail.com";

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }

}