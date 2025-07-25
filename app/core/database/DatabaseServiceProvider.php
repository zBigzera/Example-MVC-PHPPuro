<?php

namespace App\Core\Database;

use App\Core\Container;
use App\Core\Database\Database;
use \PDO;

class DatabaseServiceProvider
{
    /**
     * Registra os serviços de banco de dados no container
     */
    public static function register()
    {
        // Registra a conexão PDO como singleton
        Container::singleton(PDO::class, function() {
            return self::createConnection();
        });

        // Registra a classe Database
        Container::bind(Database::class, function() {
            return new Database(null, Container::resolve(PDO::class));
        });

        // Registra um factory para criar instâncias de Database com tabelas específicas
        Container::bind('database.factory', function() {
            return new DatabaseFactory(Container::resolve(PDO::class));
        });
    }

    /**
     * Cria uma nova conexão PDO
     * @return PDO
     */
    private static function createConnection()
    {
        // Carrega as configurações do banco de dados
        $config = self::getDatabaseConfig();

        try {
            $pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['name']};port={$config['port']}",
                $config['user'],
                $config['pass']
            );
            
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            return $pdo;
        } catch (\PDOException $e) {
  
            die('Database Connection Error: ' . $e->getMessage());
        }
    }

    /**
     * Obtém as configurações do banco de dados
     * @return array
     */
    private static function getDatabaseConfig()
    {
        
        return [
        'host' => getenv('DB_HOST') ?? 'localhost',
        'name' => getenv('DB_NAME') ?? 'mvc_pure_php',
        'user' => getenv('DB_USER') ?? 'root',
        'pass' => getenv('DB_PASS') ?? '',
        'port' => getenv('DB_PORT') ?? 3306
    ];

    

    }
}

/**
 * Factory para criar instâncias de Database com tabelas específicas
 */
class DatabaseFactory
{
    /**
     * Instância da conexão PDO
     * @var PDO
     */
    private $connection;

    /**
     * Construtor
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Cria uma nova instância de Database para uma tabela específica
     * @param string $table
     * @return Database
     */
    public function create($table)
    {
        return new Database($table, $this->connection);
    }

    /**
     * Retorna a conexão PDO
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}

