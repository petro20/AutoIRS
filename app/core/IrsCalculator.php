<?php
namespace App\Core;

use App\Models\TabelaIrs;

/**
 * Serviço de cálculo de IRS — Anexo A (rendimentos da categoria A).
 *
 * Implementa o método dos escalões com parcela a abater:
 *   coleta = rendimento_coletavel * taxa - parcela_a_abater
 *
 * Depois aplica as deduções à coleta (saúde, educação, gerais, habitação)
 * com os respetivos limites legais.
 *
 * NOTA: trata-se de um cálculo simplificado para apoio ao contabilista;
 * não substitui a liquidação oficial da Autoridade Tributária.
 */
class IrsCalculator
{
    /**
     * Calcula o IRS a partir dos valores introduzidos.
     *
     * @param array $input Campos: rendimento_categoria_a, despesas_saude,
     *                     despesas_educacao, despesas_gerais, despesas_habitacao.
     * @param int   $ano   Ano fiscal (escalões correspondentes).
     * @return array Estrutura detalhada do cálculo (guardável em JSON).
     */
    public static function calcular(array $input, int $ano = 2025): array
    {
        // --- 1. Normalizar entradas para float -------------------------
        $rendimento = self::num($input['rendimento_categoria_a'] ?? 0);
        $despSaude     = self::num($input['despesas_saude'] ?? 0);
        $despEducacao  = self::num($input['despesas_educacao'] ?? 0);
        $despGerais    = self::num($input['despesas_gerais'] ?? 0);
        $despHabitacao = self::num($input['despesas_habitacao'] ?? 0);

        // --- 2. Dedução específica da categoria A ----------------------
        // O rendimento coletável não pode ser negativo.
        $deducaoEspecifica = DEDUCAO_ESPECIFICA;
        $rendimentoColetavel = max(0, $rendimento - $deducaoEspecifica);

        // --- 3. Aplicar escalão (taxa + parcela a abater) --------------
        $escalao = TabelaIrs::escalaoPara($rendimentoColetavel, $ano);
        $taxa           = $escalao ? (float) $escalao['taxa'] : 0.0;          // ex.: 0.235
        $parcelaAbater  = $escalao ? (float) $escalao['parcela_abater'] : 0.0;

        // Coleta antes das deduções à coleta.
        $coleta = max(0, $rendimentoColetavel * $taxa - $parcelaAbater);

        // --- 4. Deduções à coleta (com limites legais) -----------------
        // Saúde: 15% das despesas, máximo 1000 €.
        $dedSaude = min($despSaude * 0.15, 1000.00);
        // Educação: 30% das despesas, máximo 800 €.
        $dedEducacao = min($despEducacao * 0.30, 800.00);
        // Despesas gerais familiares: dedução fixa de 250 € (se houver despesas).
        $dedGerais = $despGerais > 0 ? 250.00 : 0.00;
        // Habitação: 25% das despesas, máximo 800 €.
        $dedHabitacao = min($despHabitacao * 0.25, 800.00);

        $totalDeducoes = $dedSaude + $dedEducacao + $dedGerais + $dedHabitacao;

        // --- 5. Imposto final ------------------------------------------
        // A coleta não pode ficar negativa após as deduções.
        $impostoFinal = max(0, $coleta - $totalDeducoes);

        // --- 6. Estrutura detalhada (para gravar em JSON / mostrar) ----
        return [
            'ano'                 => $ano,
            'entradas' => [
                'rendimento_categoria_a' => round($rendimento, 2),
                'despesas_saude'         => round($despSaude, 2),
                'despesas_educacao'      => round($despEducacao, 2),
                'despesas_gerais'        => round($despGerais, 2),
                'despesas_habitacao'     => round($despHabitacao, 2),
            ],
            'deducao_especifica'   => round($deducaoEspecifica, 2),
            'rendimento_coletavel' => round($rendimentoColetavel, 2),
            'escalao' => [
                'limite_inferior' => $escalao['limite_inferior'] ?? null,
                'limite_superior' => $escalao['limite_superior'] ?? null,
                'taxa'            => $taxa,
                'parcela_abater'  => round($parcelaAbater, 2),
            ],
            'coleta'               => round($coleta, 2),
            'deducoes_a_coleta' => [
                'saude'     => round($dedSaude, 2),
                'educacao'  => round($dedEducacao, 2),
                'gerais'    => round($dedGerais, 2),
                'habitacao' => round($dedHabitacao, 2),
                'total'     => round($totalDeducoes, 2),
            ],
            'imposto_final' => round($impostoFinal, 2),
        ];
    }

    /**
     * Converte uma string introduzida pelo utilizador num float, aceitando
     * vírgula como separador decimal (formato português).
     */
    private static function num($valor): float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }
        $valor = str_replace([' ', '€'], '', (string) $valor);
        $valor = str_replace(',', '.', $valor);
        return is_numeric($valor) ? (float) $valor : 0.0;
    }
}
