<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Cliente;

/**
 * CRUD de clientes do contabilista autenticado.
 */
class ClienteController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    /**
     * Formulário de criação (GET) e gravação (POST).
     * Rota: /cliente/create
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::check();
            $dados = $this->validar($_POST);

            if (is_string($dados)) { // mensagem de erro
                $this->flash('danger', $dados);
                $_SESSION['old'] = $_POST;
                $this->redirect('cliente/create');
            }

            Cliente::create(Auth::id(), $dados);
            unset($_SESSION['old']);
            $this->flash('success', 'Cliente criado com sucesso.');
            $this->redirect('dashboard');
        }

        $this->view('clientes/form', [
            'titulo'  => 'Novo cliente',
            'cliente' => null,
            'action'  => url('cliente/create'),
        ]);
    }

    /**
     * Formulário de edição (GET) e atualização (POST).
     * Rota: /cliente/edit/{id}
     */
    public function edit($id = null): void
    {
        $id = (int) $id;
        $cliente = Cliente::find($id, Auth::id());
        if (!$cliente) {
            $this->flash('danger', 'Cliente não encontrado.');
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::check();
            $dados = $this->validar($_POST);

            if (is_string($dados)) {
                $this->flash('danger', $dados);
                $_SESSION['old'] = $_POST;
                $this->redirect('cliente/edit/' . $id);
            }

            Cliente::update($id, Auth::id(), $dados);
            unset($_SESSION['old']);
            $this->flash('success', 'Cliente atualizado.');
            $this->redirect('dashboard');
        }

        $this->view('clientes/form', [
            'titulo'  => 'Editar cliente',
            'cliente' => $cliente,
            'action'  => url('cliente/edit/' . $id),
        ]);
    }

    /**
     * Elimina um cliente.
     * Rota: /cliente/delete/{id}  (POST)
     */
    public function delete($id = null): void
    {
        Csrf::check();
        $id = (int) $id;
        if (Cliente::find($id, Auth::id())) {
            Cliente::delete($id, Auth::id());
            $this->flash('success', 'Cliente eliminado.');
        } else {
            $this->flash('danger', 'Cliente não encontrado.');
        }
        $this->redirect('dashboard');
    }

    /**
     * Valida e normaliza os dados do formulário de cliente.
     *
     * @return array|string Array validado, ou string com a mensagem de erro.
     */
    private function validar(array $post)
    {
        $nif      = trim($post['nif'] ?? '');
        $nome     = trim($post['nome'] ?? '');
        $email    = trim($post['email'] ?? '');
        $morada   = trim($post['morada'] ?? '');
        $contacto = trim($post['contacto'] ?? '');

        if ($nome === '') {
            return 'O nome do cliente é obrigatório.';
        }
        if (!preg_match('/^\d{9}$/', $nif)) {
            return 'O NIF deve ter exatamente 9 dígitos.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email inválido.';
        }

        return compact('nif', 'nome', 'email', 'morada', 'contacto');
    }
}
