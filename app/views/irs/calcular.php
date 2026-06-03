<?php

use App\Core\Csrf;

/**
 * @var array $cliente
 * @var array $escaloes
 */
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="bi bi-calculator"></i> <?= e(t('irs.calc_title')) ?></h1>
            <a href="<?= url('dashboard') ?>" class="btn btn-sm btn-outline-secondary"><?= e(t('common.back')) ?></a>
        </div>

        <p class="text-muted"><?= e(t('common.client')) ?>: <strong><?= e($cliente['nome']) ?></strong> (NIF <?= e($cliente['nif']) ?>)</p>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="post" action="<?= url('irs/calcular/' . $cliente['id']) ?>">
                    <?= Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label"><?= e(t('irs.fiscal_year')) ?></label>
                            <select name="ano" class="form-select">
                                <option value="2025" selected>2025</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label"><?= e(t('irs.income_a')) ?> <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="rendimento_categoria_a"
                                   class="form-control" required>
                        </div>

                        <div class="col-12"><hr class="my-2"><h6 class="text-muted"><?= e(t('irs.deductible_expenses')) ?></h6></div>

                        <div class="col-md-6 col-lg-3">
                            <label class="form-label"><?= e(t('irs.health')) ?> (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_saude" class="form-control" value="0">
                            <div class="form-text"><?= e(t('irs.health_hint')) ?></div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label"><?= e(t('irs.education')) ?> (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_educacao" class="form-control" value="0">
                            <div class="form-text"><?= e(t('irs.education_hint')) ?></div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label"><?= e(t('irs.general')) ?> (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_gerais" class="form-control" value="0">
                            <div class="form-text"><?= e(t('irs.general_hint')) ?></div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label"><?= e(t('irs.housing')) ?> (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_habitacao" class="form-control" value="0">
                            <div class="form-text"><?= e(t('irs.housing_hint')) ?></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary"><i class="bi bi-calculator"></i> <?= e(t('irs.calc_save')) ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de escalões de referência -->
        <div class="card shadow-sm">
            <div class="card-header bg-white"><i class="bi bi-table"></i> <?= e(t('irs.brackets')) ?> <?= date('Y') ?></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th><?= e(t('irs.taxable_income')) ?></th><th><?= e(t('irs.rate')) ?></th><th><?= e(t('irs.deduct_parcel')) ?></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($escaloes as $esc): ?>
                        <tr>
                            <td>
                                <?= eur($esc['limite_inferior']) ?>
                                <?php if ($esc['limite_superior'] !== null): ?>
                                    — <?= eur($esc['limite_superior']) ?>
                                <?php else: ?>
                                    <?= e(t('irs.and_above')) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($esc['taxa'] * 100, 1, ',', '.') ?>%</td>
                            <td><?= eur($esc['parcela_abater']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
