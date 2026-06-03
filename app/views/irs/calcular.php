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
            <h1 class="h3 mb-0"><i class="bi bi-calculator"></i> Calcular IRS (Anexo A)</h1>
            <a href="<?= url('dashboard') ?>" class="btn btn-sm btn-outline-secondary">Voltar</a>
        </div>

        <p class="text-muted">Cliente: <strong><?= e($cliente['nome']) ?></strong> (NIF <?= e($cliente['nif']) ?>)</p>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="post" action="<?= url('irs/calcular/' . $cliente['id']) ?>">
                    <?= Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ano fiscal</label>
                            <select name="ano" class="form-select">
                                <option value="2025" selected>2025</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Rendimento categoria A (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="rendimento_categoria_a"
                                   class="form-control" required>
                        </div>

                        <div class="col-12"><hr class="my-2"><h6 class="text-muted">Despesas dedutíveis</h6></div>

                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Saúde (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_saude" class="form-control" value="0">
                            <div class="form-text">15% (máx. 1000 €)</div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Educação (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_educacao" class="form-control" value="0">
                            <div class="form-text">30% (máx. 800 €)</div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Despesas gerais (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_gerais" class="form-control" value="0">
                            <div class="form-text">Dedução fixa 250 €</div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label">Habitação (€)</label>
                            <input type="number" step="0.01" min="0" name="despesas_habitacao" class="form-control" value="0">
                            <div class="form-text">25% (máx. 800 €)</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary"><i class="bi bi-calculator"></i> Calcular e guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de escalões de referência -->
        <div class="card shadow-sm">
            <div class="card-header bg-white"><i class="bi bi-table"></i> Escalões de IRS <?= date('Y') ?></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Rendimento coletável</th><th>Taxa</th><th>Parcela a abater</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($escaloes as $esc): ?>
                        <tr>
                            <td>
                                <?= eur($esc['limite_inferior']) ?>
                                <?php if ($esc['limite_superior'] !== null): ?>
                                    — <?= eur($esc['limite_superior']) ?>
                                <?php else: ?>
                                    e superior
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
