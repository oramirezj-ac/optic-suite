<?php

require_once __DIR__ . '/config/Config.php';

/**
 * Improved Database class with better error handling and security
 * Ejemplo de mejora: Manejo seguro de base de datos
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            Config::load();
            
            $host = Config::get('database.host');
            $dbname = Config::get('database.name');
            $charset = Config::get('database.charset');
            
            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
            
            $this->pdo = new PDO(
                $dsn,
                Config::get('database.user'),
                Config::get('database.password'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false, // Mejor seguridad
                    PDO::MYSQL_ATTR_FOUND_ROWS => true
                ]
            );
        } catch (PDOException $e) {
            // En producción, no mostrar detalles del error
            if (Config::isProduction()) {
                error_log("Database connection error: " . $e->getMessage());
                die("Error de conexión a la base de datos. Contacte al administrador.");
            } else {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO instance
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a prepared statement safely
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters for the query
     * @return PDOStatement
     * @throws DatabaseException
     */
    public function execute(string $query, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Log the error
            error_log("Database query error: " . $e->getMessage() . " Query: " . $query);
            
            // Throw custom exception
            throw new DatabaseException("Error en la consulta a la base de datos", 0, $e);
        }
    }

    /**
     * Fetch all results from a query
     */
    public function fetchAll(string $query, array $params = []): array
    {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch single result from a query
     */
    public function fetch(string $query, array $params = []): ?array
    {
        $stmt = $this->execute($query, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollback();
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Custom database exception
 */
class DatabaseException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}