<?php

use App\Models\ProcessoAbertura;

/**
 * @var array $cliente
 * @var array $processos
 */
$labels = ProcessoAbertura::ESTADO_LABELS;
$badge = [
    'rascunho' => 'secondary', 'dados_recolhidos' => 'info',
    'guia_gerado' => 'primary', 'aguarda_validacao' => 'warning',
    'concluido' => 'success', 'rejeitado' => 'danger',
];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-briefcase"></i> Abertura de atividade</h1>
    <a href="<?= url('abertura/create/' . $cliente['id']) ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Novo processo
    </a>
</div>

<p class="text-muted">Cliente: <strong><?= e($cliente['nome']) ?></strong> (NIF <?= e($cliente['nif']) ?>)</p>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($processos)): ?>
            <div class="p-4 text-center text-muted">
                Sem processos. <a href="<?= url('abertura/create/' . $cliente['id']) ?>">Iniciar um processo</a>.
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Estado</th><th>PDF</th><th>Atualizado</th><th class="text-end">Ações</th></tr>
                </thead>
                <tbody>
                <?php foreach ($processos as $p): ?>
                    <tr>
                        <td><?= e($p['id']) ?></td>
                        <td>
                            <span class="badge bg-<?= $badge[$p['estado']] ?? 'secondary' ?>">
                                <?= e($labels[$p['estado']] ?? $p['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($p['pdf_path'])): ?>
                                <i class="bi bi-file-earmark-pdf text-danger"></i> Disponível
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e(date('d/m/Y H:i', strtotime($p['updated_at']))) ?></td>
                        <td class="text-end">
                            <a href="<?= url('abertura/show/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Gerir
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
    <a href="<?= url('dashboard') ?>" class="btn btn-outline-secondary btn-sm">Voltar ao dashboard</a>
</div>
