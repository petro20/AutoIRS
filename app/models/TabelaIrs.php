<?php
namespace App\Models;

use App\Core\Database;

/**
 * Modelo das tabelas de escalões de IRS.
 *
 * Os escalões são guardados na tabela `tabelas_irs` (ver /sql/estrutura.sql)
 * o que permite atualizá-los por ano sem alterar código.
 */
class TabelaIrs
{
    /**
     * Devolve os escalões de um determinado ano, ordenados por limite inferior.
     */
    public static function escaloes(int $ano = 2025): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            'SELECT * FROM tabelas_irs WHERE ano = :ano ORDER BY limite_inferior ASC'
        );
        $stmt->execute([':ano' => $ano]);
        return $stmt->fetchAll();
    }

    /**
     * Encontra o escalão aplicável a um rendimento coletável.
     *
     * @return array|null Linha do escalão (taxa, parcela_abater, ...).
     */
    public static function escalaoPara(float $rendimentoColetavel, int $ano = 2025): ?array
    {
        foreach (self::escaloes($ano) as $escalao) {
            $inf = (float) $escalao['limite_inferior'];
            $sup = $escalao['limite_superior'] !== null
                ? (float) $escalao['limite_superior']
                : INF;

            // Intervalo [inf, sup). O último escalão tem limite_superior NULL.
            if ($rendimentoColetavel >= $inf && $rendimentoColetavel < $sup) {
                return $escalao;
            }
        }
        // Caso o valor seja superior a todos os limites, devolve o último.
        $todos = self::escaloes($ano);
        return $todos ? end($todos) : null;
    }
}
