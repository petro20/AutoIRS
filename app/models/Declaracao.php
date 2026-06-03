<?php
namespace App\Models;

use App\Core\Database;

/**
 * Modelo de declaração de IRS (resultado de um cálculo do Anexo A).
 *
 * O detalhe completo do cálculo é guardado em JSON na coluna `detalhes`,
 * permitindo reconstruir/auditar a declaração sem novas colunas.
 */
class Declaracao
{
    /**
     * Lista as declarações de um cliente.
     */
    public static function allByCliente(int $clienteId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM declaracoes WHERE cliente_id = :cid ORDER BY created_at DESC'
        );
        $stmt->execute([':cid' => $clienteId]);
        return $stmt->fetchAll();
    }

    /**
     * Procura uma declaração pelo id.
     */
    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM declaracoes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Guarda uma nova declaração.
     *
     * @param array $detalhes Estrutura completa do cálculo (será gravada em JSON).
     * @return int Id da declaração criada.
     */
    public static function create(int $clienteId, int $ano, float $impostoFinal, array $detalhes): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO declaracoes (cliente_id, ano, imposto_final, detalhes, created_at)
             VALUES (:cid, :ano, :imposto, :detalhes, NOW())'
        );
        $stmt->execute([
            ':cid'      => $clienteId,
            ':ano'      => $ano,
            ':imposto'  => $impostoFinal,
            ':detalhes' => json_encode($detalhes, JSON_UNESCAPED_UNICODE),
        ]);
        return (int) $db->lastInsertId();
    }
}
