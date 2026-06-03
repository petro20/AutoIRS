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
            <h1 class="h3 mb-0"><i class="bi bi-file-earmark-check"></i> <?= e(t('irs.result_title')) ?></h1>
            <div>
                <a href="<?= url('irs/declaracoes/' . $cliente['id']) ?>" class="btn btn-sm btn-outline-info"><?= e(t('irs.declarations')) ?></a>
                <a href="<?= url('irs/calcular/' . $cliente['id']) ?>" class="btn btn-sm btn-outline-primary"><?= e(t('irs.new_calc')) ?></a>
            </div>
        </div>

        <p class="text-muted">
            <?= e(t('common.client')) ?>: <strong><?= e($cliente['nome']) ?></strong> ·
            <?= e(t('irs.declaration')) ?> #<?= e($declId) ?> · <?= e(t('common.year')) ?> <?= e($resultado['ano']) ?>
        </p>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white"><?= e(t('irs.assessment')) ?></div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e(t('irs.income_a_short')) ?></span><strong><?= eur($ent['rendimento_categoria_a']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>(–) <?= e(t('irs.specific_deduction')) ?></span><span><?= eur($resultado['deducao_especifica']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>= <?= e(t('irs.taxable_income')) ?></span><strong><?= eur($resultado['rendimento_coletavel']) ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e(t('irs.bracket_rate')) ?></span><span><?= number_format($esc['taxa'] * 100, 1, ',', '.') ?>%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>(–) <?= e(t('irs.deduct_parcel')) ?></span><span><?= eur($esc['parcela_abater']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>= <?= e(t('irs.collection')) ?></span><strong><?= eur($resultado['coleta']) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white"><?= e(t('irs.deductions_to_coll')) ?></div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e(t('irs.health')) ?></span><span><?= eur($ded['saude']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e(t('irs.education')) ?></span><span><?= eur($ded['educacao']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e(t('irs.general')) ?></span><span><?= eur($ded['gerais']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?= e(t('irs.housing')) ?></span><span><?= eur($ded['habitacao']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <span>= <?= e(t('irs.total_deductions')) ?></span><strong><?= eur($ded['total']) ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card border-primary shadow mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h2 class="h4 mb-0"><?= e(t('irs.final_tax')) ?></h2>
                <span class="display-6 text-primary fw-bold"><?= eur($resultado['imposto_final']) ?></span>
            </div>
        </div>
    </div>
</div>
