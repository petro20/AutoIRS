<?php

use App\Core\Csrf;

/**
 * @var array $cliente
 * @var array $caeLista
 */
$o = fn(string $k) => e($_SESSION['old'][$k] ?? '');
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="h3 mb-4"><i class="bi bi-briefcase"></i> <?= e(t('ab.collect_title')) ?></h1>
        <p class="text-muted"><?= e(t('common.client')) ?>: <strong><?= e($cliente['nome']) ?></strong></p>

        <div class="alert alert-info d-flex gap-2"><i class="bi bi-info-circle-fill"></i> <span><?= e(t('tip.abertura')) ?></span></div>

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- enctype obrigatório para upload de ficheiros -->
                <form method="post" action="<?= url('abertura/create/' . $cliente['id']) ?>" enctype="multipart/form-data">
                    <?= Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label"><?= e(t('ab.start_date')) ?> <span class="text-danger">*</span></label>
                            <input type="date" name="data_inicio" class="form-control" value="<?= $o('data_inicio') ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label"><?= e(t('ab.cae')) ?></label>
                            <select name="cae" class="form-select">
                                <?php foreach ($caeLista as $cod => $desc): ?>
                                    <option value="<?= e($cod) ?>" <?= $o('cae') === (string) $cod ? 'selected' : '' ?>>
                                        <?= e($desc) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label"><?= e(t('ab.turnover')) ?></label>
                            <input type="number" step="0.01" min="0" name="volume_negocios_estimado"
                                   class="form-control" value="<?= $o('volume_negocios_estimado') ?>">
                            <div class="form-text"><?= e(t('ab.turnover_hint')) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= e(t('ab.iban')) ?> <span class="text-danger">*</span></label>
                            <input type="text" name="iban" class="form-control" value="<?= $o('iban') ?>"
                                   placeholder="PT50 0000 0000 0000 0000 0000 0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label"><?= e(t('ab.vat_option')) ?></label>
                            <select name="opcao_iva" class="form-select">
                                <option value="isencao_art53"><?= e(t('ab.vat_exempt')) ?></option>
                                <option value="regime_normal_trimestral"><?= e(t('ab.vat_normal_q')) ?></option>
                                <option value="regime_normal_mensal"><?= e(t('ab.vat_normal_m')) ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= e(t('ab.proof')) ?></label>
                            <input type="file" name="comprovativo" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text"><?= e(t('ab.proof_hint')) ?></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label"><?= e(t('ab.notes')) ?></label>
                            <textarea name="observacoes" class="form-control" rows="3"><?= $o('observacoes') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-save"></i> <?= e(t('ab.save_data')) ?></button>
                        <a href="<?= url('abertura/index/' . $cliente['id']) ?>" class="btn btn-outline-secondary"><?= e(t('common.cancel')) ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php unset($_SESSION['old']); ?>
