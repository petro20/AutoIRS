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
        <h1 class="h3 mb-4"><i class="bi bi-briefcase"></i> Recolha de dados — Abertura de atividade</h1>
        <p class="text-muted">Cliente: <strong><?= e($cliente['nome']) ?></strong></p>

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- enctype obrigatório para upload de ficheiros -->
                <form method="post" action="<?= url('abertura/create/' . $cliente['id']) ?>" enctype="multipart/form-data">
                    <?= Csrf::field() ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Data de início <span class="text-danger">*</span></label>
                            <input type="date" name="data_inicio" class="form-control" value="<?= $o('data_inicio') ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">CAE (atividade)</label>
                            <select name="cae" class="form-select">
                                <?php foreach ($caeLista as $cod => $desc): ?>
                                    <option value="<?= e($cod) ?>" <?= $o('cae') === (string) $cod ? 'selected' : '' ?>>
                                        <?= e($desc) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Volume de negócios estimado (€)</label>
                            <input type="number" step="0.01" min="0" name="volume_negocios_estimado"
                                   class="form-control" value="<?= $o('volume_negocios_estimado') ?>">
                            <div class="form-text">Determina o enquadramento de IVA (isenção art.º 53.º até 15 000 €).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">IBAN <span class="text-danger">*</span></label>
                            <input type="text" name="iban" class="form-control" value="<?= $o('iban') ?>"
                                   placeholder="PT50 0000 0000 0000 0000 0000 0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Opção de IVA</label>
                            <select name="opcao_iva" class="form-select">
                                <option value="isencao_art53">Isenção (art.º 53.º)</option>
                                <option value="regime_normal_trimestral">Regime normal — trimestral</option>
                                <option value="regime_normal_mensal">Regime normal — mensal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Comprovativo (opcional)</label>
                            <input type="file" name="comprovativo" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG ou PNG (máx. 5 MB).</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" class="form-control" rows="3"><?= $o('observacoes') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Guardar dados</button>
                        <a href="<?= url('abertura/index/' . $cliente['id']) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php unset($_SESSION['old']); ?>
