<?php
namespace App\Models;

use App\Core\Database;

/**
 * Modelo de processo de Abertura de Atividade.
 *
 * Implementa uma máquina de estados:
 *   rascunho -> dados_recolhidos -> guia_gerado -> aguarda_validacao
 *            -> concluido | rejeitado
 *
 * Os dados do formulário são guardados em JSON na coluna `dados`.
 */
class ProcessoAbertura
{
    /** Estados válidos do processo. */
    public const ESTADOS = [
        'rascunho',
        'dados_recolhidos',
        'guia_gerado',
        'aguarda_validacao',
        'concluido',
        'rejeitado',
    ];

    /** Etiquetas legíveis para apresentação na interface. */
    public const ESTADO_LABELS = [
        'rascunho'          => 'Rascunho',
        'dados_recolhidos'  => 'Dados recolhidos',
        'guia_gerado'       => 'Guia gerado',
        'aguarda_validacao' => 'Aguarda validação',
        'concluido'         => 'Concluído',
        'rejeitado'         => 'Rejeitado',
    ];

    /**
     * Lista os processos de um cliente.
     */
    public static function allByCliente(int $clienteId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM processos_abertura WHERE cliente_id = :cid ORDER BY created_at DESC'
        );
        $stmt->execute([':cid' => $clienteId]);
        return $stmt->fetchAll();
    }

    /**
     * Procura um processo pelo id.
     */
    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM processos_abertura WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cria um novo processo (estado inicial: rascunho).
     *
     * @return int Id do processo criado.
     */
    public static function create(int $clienteId, array $dados, string $estado = 'rascunho'): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO processos_abertura
                (cliente_id, dados, estado, comprovativo_path, pdf_path, created_at, updated_at)
             VALUES (:cid, :dados, :estado, :comprovativo, NULL, NOW(), NOW())'
        );
        $stmt->execute([
            ':cid'          => $clienteId,
            ':dados'        => json_encode($dados, JSON_UNESCAPED_UNICODE),
            ':estado'       => $estado,
            ':comprovativo' => $dados['comprovativo_path'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Atualiza os dados e o estado de um processo.
     */
    public static function update(int $id, array $dados, string $estado, ?string $comprovativoPath = null): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'UPDATE processos_abertura
                SET dados = :dados, estado = :estado,
                    comprovativo_path = COALESCE(:comprovativo, comprovativo_path),
                    updated_at = NOW()
              WHERE id = :id'
        );
        return $stmt->execute([
            ':dados'        => json_encode($dados, JSON_UNESCAPED_UNICODE),
            ':estado'       => $estado,
            ':comprovativo' => $comprovativoPath,
            ':id'           => $id,
        ]);
    }

    /**
     * Atualiza apenas o estado do processo.
     */
    public static function updateEstado(int $id, string $estado): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'UPDATE processos_abertura SET estado = :estado, updated_at = NOW() WHERE id = :id'
        );
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    /**
     * Guarda o caminho do PDF gerado e avança o estado para guia_gerado.
     */
    public static function setPdf(int $id, string $pdfPath): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'UPDATE processos_abertura
                SET pdf_path = :pdf, estado = :estado, updated_at = NOW()
              WHERE id = :id'
        );
        return $stmt->execute([
            ':pdf'    => $pdfPath,
            ':estado' => 'guia_gerado',
            ':id'     => $id,
        ]);
    }
}
