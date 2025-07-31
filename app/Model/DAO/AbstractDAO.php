<?php
namespace App\Model\DAO;

use App\Core\Database\Database;

abstract class AbstractDAO
{
    protected Database $database;
    protected string $table;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    // Métodos genéricos: find, insert, update, delete etc.
}
