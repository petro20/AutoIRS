<?php

use App\Core\Csrf;

/**
 * @var array $processo
 * @var array $cliente
 * @var array $dados
 * @var array $labels
 */
$estado = $processo['estado'];
$badge = [
    'rascunho' => 'secondary', 'dados_recolhidos' => 'info',
    'guia_gerado' => 'primary', 'aguarda_validacao' => 'warning',
    'concluido' => 'success', 'rejeitado' => 'danger',
];

// Ordem dos estados para a barra de progresso.
$fluxo = ['dados_recolhidos', 'guia_gerado', 'aguarda_validacao', 'concluido'];
$posAtual = array_search($estado, $fluxo, true);
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-briefcase"></i> Processo #<?= e($processo['id']) ?></h1>
    <a href="<?= url('abertura/index/' . $cliente['id']) ?>" class="btn btn-sm btn-outline-secondary">Voltar</a>
</div>

<p>
    Cliente: <strong><?= e($cliente['nome']) ?></strong> ·
    Estado: <span class="badge bg-<?= $badge[$estado] ?? 'secondary' ?>"><?= e($labels[$estado] ?? $estado) ?></span>
</p>

<!-- Barra de progresso do fluxo -->
<?php if ($estado !== 'rejeitado'): ?>
<div class="d-flex mb-4">
    <?php foreach ($fluxo as $i => $passo): ?>
        <div class="flex-fill text-center">
            <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center
                        <?= ($posAtual !== false && $i <= $posAtual) ? 'bg-primary text-white' : 'bg-light text-muted' ?>"
                 style="width:36px;height:36px;"><?= $i + 1 ?></div>
            <small class="<?= ($posAtual !== false && $i <= $posAtual) ? 'fw-semibold' : 'text-muted' ?>">
                <?= e($labels[$passo]) ?>
            </small>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Dados recolhidos -->
    <div class="col-md-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><i class="bi bi-clipboard-data"></i> Dados recolhidos</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span>Data de início</span><strong><?= e($dados['data_inicio'] ?? '—') ?></strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>CAE</span><strong><?= e($dados['cae'] ?? '—') ?></strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>Volume de negócios estimado</span><strong><?= eur($dados['volume_negocios_estimado'] ?? 0) ?></strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>IBAN</span><strong><?= e($dados['iban'] ?? '—') ?></strong></li>
                <li class="list-group-item d-flex justify-content-between"><span>Opção de IVA</span><strong><?= e($dados['opcao_iva'] ?? '—') ?></strong></li>
                <?php if (!empty($dados['observacoes'])): ?>
                    <li class="list-group-item"><span class="text-muted">Observações:</span><br><?= nl2br(e($dados['observacoes'])) ?></li>
                <?php endif; ?>
                <?php if (!empty($processo['comprovativo_path'])): ?>
                    <li class="list-group-item">
                        <i class="bi bi-paperclip"></i>
                        <a href="<?= url('abertura/comprovativo/' . $processo['id']) ?>" target="_blank">Ver comprovativo</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Ações disponíveis (máquina de estados) -->
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><i class="bi bi-gear"></i> Ações</div>
            <div class="card-body d-grid gap-2">

                <!-- Gerar guia PDF: disponível após recolha de dados -->
                <?php if (in_array($estado, ['dados_recolhidos', 'guia_gerado', 'aguarda_validacao'], true)): ?>
                    <form method="post" action="<?= url('abertura/gerarGuia/' . $processo['id']) ?>">
                        <?= Csrf::field() ?>
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <?= empty($processo['pdf_path']) ? 'Gerar guia PDF' : 'Regerar guia PDF' ?>
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Download do PDF -->
                <?php if (!empty($processo['pdf_path'])): ?>
                    <a href="<?= url('abertura/download/' . $processo['id']) ?>" target="_blank" class="btn btn-outline-danger">
                        <i class="bi bi-download"></i> Ver / descarregar guia
                    </a>
                <?php endif; ?>

                <!-- Enviar para validação -->
                <?php if ($estado === 'guia_gerado'): ?>
                    <form method="post" action="<?= url('abertura/enviarValidacao/' . $processo['id']) ?>">
                        <?= Csrf::field() ?>
                        <button class="btn btn-warning w-100"><i class="bi bi-send"></i> Enviar para validação</button>
                    </form>
                <?php endif; ?>

                <!-- Aprovar / Rejeitar (validação do contabilista) -->
                <?php if ($estado === 'aguarda_validacao'): ?>
                    <form method="post" action="<?= url('abertura/aprovar/' . $processo['id']) ?>">
                        <?= Csrf::field() ?>
                        <button class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Aprovar e concluir</button>
                    </form>
                    <form method="post" action="<?= url('abertura/rejeitar/' . $processo['id']) ?>"
                          onsubmit="return confirm('Rejeitar este processo?');">
                        <?= Csrf::field() ?>
                        <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Rejeitar</button>
                    </form>
                <?php endif; ?>

                <?php if ($estado === 'concluido'): ?>
                    <div class="alert alert-success mb-0"><i class="bi bi-check-circle"></i> Processo concluído.</div>
                <?php elseif ($estado === 'rejeitado'): ?>
                    <div class="alert alert-danger mb-0"><i class="bi bi-x-circle"></i> Processo rejeitado.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
