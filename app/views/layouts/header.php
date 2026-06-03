<?php

use App\Core\Auth;
use App\Core\I18n;

/** @var string $titulo */
?>
<!DOCTYPE html>
<html lang="pt" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? APP_NAME) ?> — <?= APP_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= url('assets/img/logo.png') ?>">

    <!-- Tipografia premium -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-luxe sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= url('dashboard') ?>">
            <img src="<?= url('assets/img/logo.png') ?>" alt="AutoIRS" height="46" style="border-radius:8px">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('dashboard') ?>"><i class="bi bi-speedometer2"></i> <?= e(t('nav.dashboard')) ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('cliente/create') ?>"><i class="bi bi-person-plus"></i> <?= e(t('nav.new_client')) ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('help') ?>"><i class="bi bi-question-circle"></i> <?= e(t('nav.help')) ?></a>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-white-50"><i class="bi bi-person-circle"></i> <?= e(Auth::nome()) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('auth/logout') ?>"><i class="bi bi-box-arrow-right"></i> <?= e(t('nav.logout')) ?></a>
                </li>
                <!-- Seletor de idioma -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="<?= e(t('lang.label')) ?>">
                        <i class="bi bi-translate"></i> <?= I18n::FLAGS[I18n::lang()] ?? '' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php foreach (I18n::SUPPORTED as $code => $name): ?>
                            <li>
                                <a class="dropdown-item<?= I18n::lang() === $code ? ' active' : '' ?>" href="<?= url('lang/set/' . $code) ?>">
                                    <?= I18n::FLAGS[$code] ?> <?= e($name) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= e($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash']['message'] // já escapado quando necessário ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
