<?php
/**
 * @var array $cliente
 * @var array $resultado  Estrutura devolvida por IrsCalculator::calcular()
 * @var int   $declId
 */
$ent = $resultado['entradas'];
$ded = $resultado['deducoes_a_coleta'];
$esc = $resultado['escalao'];
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="bi bi-file-earmark-check"></i> Resultado do cálculo</h1>
            <div>
                <a href="<?= url('irs/declaracoes/' . $cliente['id']) ?>" class="btn btn-sm btn-outline-info">Declarações</a>
                <a href="<?= url('irs/calcular/' . $cliente['id']) ?>" class="btn btn-sm btn-outline-primary">Novo cálculo</a>
            </div>
        </div>

        <p class="text-muted">
            Cliente: <strong><?= e($cliente['nome']) ?></strong> ·
            Declaração #<?= e($declId) ?> · Ano <?= e($resultado['ano']) ?>
        </p>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">Apuramento da coleta</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Rendimento categoria A</span><strong><?= eur($ent['rendimento_categoria_a']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>(–) Dedução específica</span><span><?= eur($resultado['deducao_especifica']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>= Rendimento coletável</span><strong><?= eur($resultado['rendimento_coletavel']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Taxa do escalão</span><span><?= number_format($esc['taxa'] * 100, 1, ',', '.') ?>%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>(–) Parcela a abater</span><span><?= eur($esc['parcela_abater']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>= Coleta</span><strong><?= eur($resultado['coleta']) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">Deduções à coleta</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Saúde</span><span><?= eur($ded['saude']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Educação</span><span><?= eur($ded['educacao']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Despesas gerais</span><span><?= eur($ded['gerais']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Habitação</span><span><?= eur($ded['habitacao']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>= Total deduções</span><strong><?= eur($ded['total']) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card border-primary shadow mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0">Imposto final estimado</h2>
                <span class="display-6 text-primary fw-bold"><?= eur($resultado['imposto_final']) ?></span>
            </div>
        </div>
    </div>
</div>
