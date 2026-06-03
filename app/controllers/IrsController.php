<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\IrsCalculator;
use App\Models\Cliente;
use App\Models\Declaracao;
use App\Models\TabelaIrs;

/**
 * Controlador de cálculo de IRS (Anexo A) e gestão de declarações.
 */
class IrsController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    /**
     * Formulário de cálculo (GET) e processamento (POST).
     * Rota: /irs/calcular/{cliente_id}
     */
    public function calcular($clienteId = null): void
    {
        $clienteId = (int) $clienteId;
        $cliente = Cliente::find($clienteId, Auth::id());
        if (!$cliente) {
            $this->flash('danger', 'Cliente não encontrado.');
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::check();

            $ano = (int) ($_POST['ano'] ?? 2025);
            $resultado = IrsCalculator::calcular($_POST, $ano);

            // Guarda a declaração com o detalhe em JSON.
            $declId = Declaracao::create(
                $clienteId,
                $ano,
                $resultado['imposto_final'],
                $resultado
            );

            $this->view('irs/resultado', [
                'titulo'    => 'Resultado do cálculo de IRS',
                'cliente'   => $cliente,
                'resultado' => $resultado,
                'declId'    => $declId,
            ]);
            return;
        }

        $this->view('irs/calcular', [
            'titulo'    => 'Calcular IRS — ' . $cliente['nome'],
            'cliente'   => $cliente,
            'escaloes'  => TabelaIrs::escaloes(2025),
        ]);
    }

    /**
     * Lista as declarações de um cliente.
     * Rota: /irs/declaracoes/{cliente_id}
     */
    public function declaracoes($clienteId = null): void
    {
        $clienteId = (int) $clienteId;
        $cliente = Cliente::find($clienteId, Auth::id());
        if (!$cliente) {
            $this->flash('danger', 'Cliente não encontrado.');
            $this->redirect('dashboard');
        }

        $this->view('irs/declaracoes', [
            'titulo'      => 'Declarações — ' . $cliente['nome'],
            'cliente'     => $cliente,
            'declaracoes' => Declaracao::allByCliente($clienteId),
        ]);
    }

    /**
     * Mostra o detalhe de uma declaração guardada.
     * Rota: /irs/ver/{declaracao_id}
     */
    public function ver($id = null): void
    {
        $id = (int) $id;
        $declaracao = Declaracao::find($id);
        if (!$declaracao) {
            $this->flash('danger', 'Declaração não encontrada.');
            $this->redirect('dashboard');
        }

        // Verifica que o cliente da declaração pertence ao contabilista.
        $cliente = Cliente::find((int) $declaracao['cliente_id'], Auth::id());
        if (!$cliente) {
            $this->flash('danger', 'Sem permissão para ver esta declaração.');
            $this->redirect('dashboard');
        }

        $this->view('irs/resultado', [
            'titulo'    => 'Declaração #' . $id,
            'cliente'   => $cliente,
            'resultado' => json_decode($declaracao['detalhes'], true),
            'declId'    => $id,
        ]);
    }
}
