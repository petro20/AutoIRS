<?php
/**
 * @var array $cliente
 * @var array $declaracoes
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-file-earmark-text"></i> <?= e(t('irs.decl_list_title')) ?></h1>
    <a href="<?= url('irs/calcular/' . $cliente['id']) ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> <?= e(t('irs.new_calc')) ?>
    </a>
</div>

<p class="text-muted"><?= e(t('common.client')) ?>: <strong><?= e($cliente['nome']) ?></strong></p>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($declaracoes)): ?>
            <div class="p-4 text-center text-muted"><?= e(t('irs.no_declarations')) ?></div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th><?= e(t('common.year')) ?></th><th><?= e(t('irs.final_tax_col')) ?></th><th><?= e(t('common.date')) ?></th><th class="text-end"><?= e(t('common.actions')) ?></th></tr>
                </thead>
                <tbody>
                <?php foreach ($declaracoes as $d): ?>
                    <tr>
                        <td><?= e($d['id']) ?></td>
                        <td><?= e($d['ano']) ?></td>
                        <td class="fw-semibold"><?= eur($d['imposto_final']) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($d['created_at']))) ?></td>
                        <td class="text-end">
                            <a href="<?= url('irs/ver/' . $d['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> <?= e(t('irs.view_detail')) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="mt-3">
    <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary btn-sm"><?= e(t('common.back_dashboard')) ?></a>
</div>
