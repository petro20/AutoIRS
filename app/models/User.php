<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo de contabilista (utilizador da aplicação).
 */
class User
{
    /**
     * Procura um utilizador pelo email.
     */
    public static function findByEmail(string $email): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Procura um utilizador pelo id.
     */
    public static function findById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Cria um novo contabilista. A password é guardada com bcrypt.
     *
     * @return int Id do utilizador criado.
     */
    public static function create(string $nome, string $email, string $password): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO users (nome, email, password_hash, created_at)
             VALUES (:nome, :email, :hash, NOW())'
        );
        $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            // PASSWORD_BCRYPT = algoritmo bcrypt.
            ':hash'  => password_hash($password, PASSWORD_BCRYPT),
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Verifica as credenciais e devolve o utilizador se forem válidas.
     */
    public static function verifyCredentials(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }
}
