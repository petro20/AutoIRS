<?php
/** Página de Ajuda — acordeão com os principais tópicos. */
$topicos = [
    ['icon' => 'people',     'title' => 'help.c1_title', 'body' => 'help.c1_body'],
    ['icon' => 'calculator', 'title' => 'help.c2_title', 'body' => 'help.c2_body'],
    ['icon' => 'briefcase',  'title' => 'help.c3_title', 'body' => 'help.c3_body'],
    ['icon' => 'translate',  'title' => 'help.c4_title', 'body' => 'help.c4_body'],
];
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="h3 mb-1"><i class="bi bi-question-circle"></i> <?= e(t('help.title')) ?></h1>
        <p class="text-muted mb-4"><?= e(t('help.subtitle')) ?></p>

        <div class="accordion" id="ajudaAccordion">
            <?php foreach ($topicos as $i => $top): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $i === 0 ? '' : 'collapsed' ?>" type="button"
                                data-bs-toggle="collapse" data-bs-target="#topico<?= $i ?>"
                                aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>">
                            <i class="bi bi-<?= e($top['icon']) ?> me-2"></i> <?= e(t($top['title'])) ?>
                        </button>
                    </h2>
                    <div id="topico<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
                         data-bs-parent="#ajudaAccordion">
                        <div class="accordion-body"><?= e(t($top['body'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="alert alert-warning mt-4">
            <i class="bi bi-exclamation-triangle"></i> <?= e(t('footer.disclaimer')) ?>
        </div>

        <div class="mt-3">
            <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary btn-sm"><?= e(t('common.back_dashboard')) ?></a>
        </div>
    </div>
</div>
