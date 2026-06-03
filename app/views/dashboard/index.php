<?php

use App\Core\Csrf;

/**
 * @var array $clientes
 * @var array $alertas
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-speedometer2"></i> <?= e(t('dash.title')) ?></h1>
    <a href="<?= url('cliente/create') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> <?= e(t('dash.new_client')) ?>
    </a>
</div>

<!-- Alertas de prazos / validações pendentes -->
<?php if (!empty($alertas)): ?>
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning-subtle">
            <i class="bi bi-bell"></i> <?= e(t('dash.alerts')) ?> (<?= count($alertas) ?>)
        </div>
        <ul class="list-group list-group-flush">
            <?php foreach ($alertas as $a): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong><?= e($a['cliente']) ?></strong> — <?= e($a['mensagem']) ?></span>
                    <a href="<?= url($a['link']) ?>" class="btn btn-sm btn-outline-warning">Ver</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Lista de clientes -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <i class="bi bi-people"></i> <?= e(t('dash.my_clients')) ?> (<?= count($clientes) ?>)
    </div>
    <div class="card-body p-0">
        <?php if (empty($clientes)): ?>
            <div class="p-4 text-center text-muted">
                <?= e(t('dash.no_clients')) ?> <a href="<?= url('cliente/create') ?>"><?= e(t('dash.add_first')) ?></a>.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= e(t('dash.col_name')) ?></th>
                            <th><?= e(t('dash.col_nif')) ?></th>
                            <th><?= e(t('dash.col_email')) ?></th>
                            <th><?= e(t('dash.col_contact')) ?></th>
                            <th class="text-end"><?= e(t('dash.col_actions')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($c['nome']) ?></td>
                            <td><?= e($c['nif']) ?></td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= e($c['contacto']) ?></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('cliente/edit/' . $c['id']) ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= url('irs/calcular/' . $c['id']) ?>" class="btn btn-outline-primary" title="Calcular IRS">
                                        <i class="bi bi-calculator"></i>
                                    </a>
                                    <a href="<?= url('irs/declaracoes/' . $c['id']) ?>" class="btn btn-outline-info" title="Declarações">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    <a href="<?= url('abertura/index/' . $c['id']) ?>" class="btn btn-outline-success" title="Abertura de atividade">
                                        <i class="bi bi-briefcase"></i>
                                    </a>
                                    <form method="post" action="<?= url('cliente/delete/' . $c['id']) ?>"
                                          onsubmit="return confirm('Eliminar este cliente? Esta ação é irreversível.');"
                                          class="d-inline">
                                        <?= Csrf::field() ?>
                                        <button class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
