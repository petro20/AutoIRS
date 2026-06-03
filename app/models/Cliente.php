<?php
namespace App\Models;

use App\Core\Database;

/**
 * Modelo de cliente. Cada cliente pertence a um contabilista (user_id).
 *
 * Todas as consultas filtram por user_id para garantir que um contabilista
 * só acede aos seus próprios clientes (isolamento de dados / multi-tenant).
 */
class Cliente
{
    /**
     * Lista todos os clientes de um contabilista.
     */
    public static function allByUser(int $userId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM clientes WHERE user_id = :uid ORDER BY nome ASC'
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Procura um cliente garantindo que pertence ao contabilista.
     */
    public static function find(int $id, int $userId): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM clientes WHERE id = :id AND user_id = :uid LIMIT 1'
        );
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cria um novo cliente associado ao contabilista.
     *
     * @return int Id do cliente criado.
     */
    public static function create(int $userId, array $d): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO clientes (user_id, nif, nome, email, morada, contacto, created_at)
             VALUES (:uid, :nif, :nome, :email, :morada, :contacto, NOW())'
        );
        $stmt->execute([
            ':uid'      => $userId,
            ':nif'      => $d['nif'],
            ':nome'     => $d['nome'],
            ':email'    => $d['email'],
            ':morada'   => $d['morada'],
            ':contacto' => $d['contacto'],
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Atualiza um cliente (apenas se pertencer ao contabilista).
     */
    public static function update(int $id, int $userId, array $d): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'UPDATE clientes
                SET nif = :nif, nome = :nome, email = :email,
                    morada = :morada, contacto = :contacto
              WHERE id = :id AND user_id = :uid'
        );
        return $stmt->execute([
            ':nif'      => $d['nif'],
            ':nome'     => $d['nome'],
            ':email'    => $d['email'],
            ':morada'   => $d['morada'],
            ':contacto' => $d['contacto'],
            ':id'       => $id,
            ':uid'      => $userId,
        ]);
    }

    /**
     * Elimina um cliente (apenas se pertencer ao contabilista).
     */
    public static function delete(int $id, int $userId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'DELETE FROM clientes WHERE id = :id AND user_id = :uid'
        );
        return $stmt->execute([':id' => $id, ':uid' => $userId]);
    }
}
