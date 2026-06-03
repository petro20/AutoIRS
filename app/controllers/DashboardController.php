<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Cliente;
use App\Models\ProcessoAbertura;

/**
 * Dashboard do contabilista: visão geral de clientes, processos e alertas.
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        // Todas as ações do dashboard exigem autenticação.
        Auth::requireLogin();
    }

    /**
     * Página inicial após login.
     * Rota: / ou /dashboard
     */
    public function index(): void
    {
        $userId   = Auth::id();
        $clientes = Cliente::allByUser($userId);

        // --- Alertas de prazos (estrutura preparada para cron jobs) -----
        // Por agora, listamos processos pendentes de validação como alertas.
        // Um cron job futuro poderá popular uma tabela `alertas` com prazos
        // fiscais (entrega de IRS, pagamentos por conta, etc.).
        $alertas = [];
        foreach ($clientes as $cliente) {
            $processos = ProcessoAbertura::allByCliente((int) $cliente['id']);
            foreach ($processos as $p) {
                if ($p['estado'] === 'aguarda_validacao') {
                    $alertas[] = [
                        'cliente' => $cliente['nome'],
                        'mensagem' => 'Processo de abertura de atividade aguarda a sua validação.',
                        'link' => 'abertura/show/' . $p['id'],
                    ];
                }
            }
        }

        $this->view('dashboard/index', [
            'titulo'   => 'Dashboard',
            'clientes' => $clientes,
            'alertas'  => $alertas,
        ]);
    }
}
