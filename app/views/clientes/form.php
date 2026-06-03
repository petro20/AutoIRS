<?php

use App\Core\Csrf;

/**
 * Formulário partilhado de criação/edição de cliente.
 *
 * @var array|null $cliente  Dados existentes (edição) ou null (criação).
 * @var string     $action   URL de submissão.
 */
$v = fn(string $k) => e($cliente[$k] ?? ($_SESSION['old'][$k] ?? ''));
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h3 mb-4"><i class="bi bi-person-vcard"></i> <?= e(t($cliente ? 'cli.edit' : 'cli.new')) ?></h1>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= e($action) ?>">
                    <?= Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label"><?= e(t('cli.name')) ?> <span class="text-danger">*</span></label>
                            <input type="text" name="nome" class="form-control" value="<?= $v('nome') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= e(t('cli.nif')) ?> <span class="text-danger">*</span></label>
                            <input type="text" name="nif" class="form-control" value="<?= $v('nif') ?>"
                                   pattern="\d{9}" maxlength="9" placeholder="<?= e(t('cli.nif_hint')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= e(t('cli.email')) ?></label>
                            <input type="email" name="email" class="form-control" value="<?= $v('email') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= e(t('cli.contact')) ?></label>
                            <input type="text" name="contacto" class="form-control" value="<?= $v('contacto') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label"><?= e(t('cli.address')) ?></label>
                            <textarea name="morada" class="form-control" rows="2"><?= $v('morada') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-check-lg"></i> <?= e(t('common.save')) ?></button>
                        <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary"><?= e(t('common.cancel')) ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php unset($_SESSION['old']); ?>
