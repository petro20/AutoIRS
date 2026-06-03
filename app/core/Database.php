<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Wrapper de ligação PDO à base de dados (padrão Singleton).
 *
 * Usa exclusivamente prepared statements através dos métodos auxiliares,
 * garantindo proteção contra SQL Injection.
 */
class Database
{
    /** @var PDO|null Instância única da ligação. */
    private static ?PDO $instance = null;

    /**
     * Devolve (criando se necessário) a ligação PDO partilhada.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // Carrega as credenciais de /config/database.php
            $config = require ROOT_PATH . '/config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['dbname'],
                $config['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // lança exceções
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // arrays associativos
                PDO::ATTR_EMULATE_PREPARES   => false,                   // prepared statements reais
            ];

            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], $options);
            } catch (PDOException $e) {
                // Em produção não expomos detalhes da exceção ao utilizador.
                if (APP_ENV === 'development') {
                    die('Erro de ligação à base de dados: ' . $e->getMessage());
                }
                die('Erro de ligação à base de dados. Contacte o administrador.');
            }
        }

        return self::$instance;
    }
}
