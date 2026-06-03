<?php
/**
 * Modelo HTML do "Guia Personalizado para Abertura de Atividade".
 * É renderizado pelo dompdf (PdfService) — usar HTML/CSS simples e inline.
 *
 * @var array $cliente
 * @var array $dados
 * @var array $processo
 * @var array $caeLista
 */
$caeDesc = $caeLista[$dados['cae']] ?? ($dados['cae'] ?? '—');

// Texto do enquadramento de IVA conforme a opção.
$ivaTexto = [
    'isencao_art53'            => 'Isenção ao abrigo do artigo 53.º do CIVA (aplicável a volume de negócios anual até 15 000 €).',
    'regime_normal_trimestral' => 'Regime normal de IVA com periodicidade trimestral.',
    'regime_normal_mensal'     => 'Regime normal de IVA com periodicidade mensal.',
][$dados['opcao_iva'] ?? ''] ?? 'A confirmar com o contabilista.';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #222; }
        h1 { color: #0d6efd; font-size: 20px; margin-bottom: 0; }
        h2 { font-size: 14px; border-bottom: 2px solid #0d6efd; padding-bottom: 4px; margin-top: 24px; }
        .sub { color: #666; font-size: 11px; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
        td.label { width: 40%; color: #555; }
        ol { margin: 8px 0 8px 18px; padding: 0; }
        ol li { margin-bottom: 6px; }
        .footer { margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }
        .box { background: #f3f7ff; border: 1px solid #cfe0ff; padding: 10px; margin-top: 8px; }
    </style>
</head>
<body>

    <h1>Guia Personalizado para Abertura de Atividade</h1>
    <div class="sub">
        AutoIRS · Documento gerado em <?= date('d/m/Y H:i') ?> · Processo #<?= e($processo['id']) ?>
    </div>

    <h2>Dados do contribuinte</h2>
    <table>
        <tr><td class="label">Nome</td><td><?= e($cliente['nome']) ?></td></tr>
        <tr><td class="label">NIF</td><td><?= e($cliente['nif']) ?></td></tr>
        <tr><td class="label">Email</td><td><?= e($cliente['email']) ?></td></tr>
        <tr><td class="label">Contacto</td><td><?= e($cliente['contacto']) ?></td></tr>
        <tr><td class="label">Morada</td><td><?= e($cliente['morada']) ?></td></tr>
    </table>

    <h2>Dados da atividade</h2>
    <table>
        <tr><td class="label">Data de início prevista</td><td><?= e($dados['data_inicio'] ?? '—') ?></td></tr>
        <tr><td class="label">CAE</td><td><?= e($caeDesc) ?></td></tr>
        <tr><td class="label">Volume de negócios estimado</td><td><?= eur($dados['volume_negocios_estimado'] ?? 0) ?></td></tr>
        <tr><td class="label">IBAN</td><td><?= e($dados['iban'] ?? '—') ?></td></tr>
        <tr><td class="label">Enquadramento de IVA</td><td><?= e($ivaTexto) ?></td></tr>
        <?php if (!empty($dados['observacoes'])): ?>
            <tr><td class="label">Observações</td><td><?= nl2br(e($dados['observacoes'])) ?></td></tr>
        <?php endif; ?>
    </table>

    <h2>Instruções para a abertura de atividade</h2>
    <ol>
        <li>Aceda ao <strong>Portal das Finanças</strong> (www.portaldasfinancas.gov.pt) com as suas credenciais de acesso.</li>
        <li>No menu <em>Cidadãos &gt; Serviços &gt; Início de Atividade</em>, selecione <strong>"Entregar Declaração"</strong>.</li>
        <li>Indique a <strong>data de início</strong> (<?= e($dados['data_inicio'] ?? '—') ?>) e o <strong>CAE</strong> correspondente (<?= e($dados['cae'] ?? '—') ?>).</li>
        <li>Selecione o <strong>enquadramento de IVA</strong> indicado acima.</li>
        <li>Introduza o <strong>IBAN</strong> para eventuais reembolsos: <?= e($dados['iban'] ?? '—') ?>.</li>
        <li>Confirme o enquadramento em sede de <strong>IRS (categoria B)</strong> — regime simplificado ou contabilidade organizada.</li>
        <li>Submeta a declaração e guarde o <strong>comprovativo</strong> de entrega.</li>
    </ol>

    <div class="box">
        <strong>Nota:</strong> Este guia foi preparado pelo seu contabilista com base nos dados fornecidos.
        Antes de submeter, confirme todos os elementos. O contabilista validará o processo na plataforma AutoIRS.
    </div>

    <div class="footer">
        Documento gerado automaticamente pela plataforma AutoIRS (autoirs.com).
        Não tem valor de declaração oficial perante a Autoridade Tributária.
    </div>

</body>
</html>
