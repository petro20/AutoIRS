<?php

use App\Core\Csrf;
use App\Core\I18n;

/** Vista de login (sem layout principal). */
?>
<!DOCTYPE html>
<html lang="<?= e(I18n::lang()) ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(t('auth.login')) ?> — <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= url('assets/img/logo.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="auth-page">
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height:100vh">
        <div class="col-md-5 col-lg-4">
            <div class="auth-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <img src="<?= url('assets/img/logo.png') ?>" alt="AutoIRS" style="width:230px;max-width:78%;border-radius:16px">
                        <p class="text-muted mt-2 mb-0" style="letter-spacing:2px;text-transform:uppercase;font-size:.72rem"><?= e(t('auth.area')) ?></p>
                    </div>

                    <?php if (!empty($_SESSION['flash'])): ?>
                        <div class="alert alert-<?= e($_SESSION['flash']['type']) ?>">
                            <?= $_SESSION['flash']['message'] ?>
                        </div>
                        <?php unset($_SESSION['flash']); ?>
                    <?php endif; ?>

                    <form method="post" action="<?= url('auth/login') ?>">
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label class="form-label"><?= e(t('auth.email')) ?></label>
                            <input type="email" name="email" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= e(t('auth.password')) ?></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-senha" type="button" tabindex="-1" aria-label="<?= e(t('auth.show_password')) ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> <?= e(t('auth.login')) ?></button>
                    </form>

                    <hr class="auth-divider my-4">
                    <p class="text-center mb-0">
                        <?= e(t('auth.no_account')) ?> <a href="<?= url('auth/register') ?>"><?= e(t('auth.register')) ?></a>
                    </p>

                    <!-- Seletor de idioma -->
                    <div class="text-center mt-3">
                        <?php foreach (I18n::SUPPORTED as $code => $name): ?>
                            <a href="<?= url('lang/set/' . $code) ?>" class="text-decoration-none small <?= I18n::lang() === $code ? 'fw-bold' : 'text-muted' ?>" style="margin:0 .3rem" title="<?= e($name) ?>"><?= I18n::FLAGS[$code] ?></a>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-center text-muted small mt-3 mb-0" style="letter-spacing:1px">AutoIRS · Premium v1.0</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Mostrar/ocultar senha (olhinho)
    document.querySelectorAll('.toggle-senha').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp = this.parentElement.querySelector('input');
            var icon = this.querySelector('i');
            var mostrar = inp.type === 'password';
            inp.type = mostrar ? 'text' : 'password';
            icon.className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
</script>
</body>
</html>
