<?php

use App\Core\Csrf;

/** Vista de registo (sem layout principal). */
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar — <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-primary bg-gradient">
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height:100vh">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <h1 class="h3 text-center mb-1"><i class="bi bi-calculator text-primary"></i> <?= APP_NAME ?></h1>
                    <p class="text-center text-muted mb-4">Criar conta de contabilista</p>

                    <?php if (!empty($_SESSION['flash'])): ?>
                        <div class="alert alert-<?= e($_SESSION['flash']['type']) ?>">
                            <?= $_SESSION['flash']['message'] ?>
                        </div>
                        <?php unset($_SESSION['flash']); ?>
                    <?php endif; ?>

                    <form method="post" action="<?= url('auth/register') ?>">
                        <?= Csrf::field() ?>
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?= old('nome') ?>" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                            <div class="form-text">Mínimo 8 caracteres.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar password</label>
                            <input type="password" name="password_confirm" class="form-control" minlength="8" required>
                        </div>
                        <button class="btn btn-primary w-100"><i class="bi bi-person-plus"></i> Criar conta</button>
                    </form>

                    <hr class="my-4">
                    <p class="text-center mb-0">
                        Já tem conta? <a href="<?= url('auth/login') ?>">Entrar</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php unset($_SESSION['old']); ?>
