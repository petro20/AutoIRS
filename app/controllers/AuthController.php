<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\User;

/**
 * Controlador de autenticação: registo, login e logout de contabilistas.
 */
class AuthController extends Controller
{
    /**
     * Formulário de login (GET) e processamento (POST).
     * Rota: /auth/login
     */
    public function login(): void
    {
        // Se já estiver autenticado, vai para o dashboard.
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::check();

            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::verifyCredentials($email, $password);
            if ($user) {
                Auth::login($user);
                $this->flash('success', 'Bem-vindo, ' . e($user['nome']) . '!');
                $this->redirect('dashboard');
            }

            $this->flash('danger', 'Credenciais inválidas.');
            $this->redirect('auth/login');
        }

        $this->viewBare('auth/login', ['titulo' => 'Entrar']);
    }

    /**
     * Formulário de registo (GET) e processamento (POST).
     * Rota: /auth/register
     */
    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::check();

            $nome     = trim($_POST['nome'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password_confirm'] ?? '';

            // --- Validações ----------------------------------------------
            $erros = [];
            if ($nome === '') {
                $erros[] = 'O nome é obrigatório.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'Email inválido.';
            }
            if (strlen($password) < 8) {
                $erros[] = 'A password deve ter pelo menos 8 caracteres.';
            }
            if ($password !== $password2) {
                $erros[] = 'As passwords não coincidem.';
            }
            if (User::findByEmail($email)) {
                $erros[] = 'Já existe uma conta com este email.';
            }

            if ($erros) {
                $this->flash('danger', implode(' ', $erros));
                $_SESSION['old'] = ['nome' => $nome, 'email' => $email];
                $this->redirect('auth/register');
            }

            // --- Criação -------------------------------------------------
            $id = User::create($nome, $email, $password);
            Auth::login(['id' => $id, 'nome' => $nome, 'email' => $email]);
            unset($_SESSION['old']);
            $this->flash('success', 'Conta criada com sucesso!');
            $this->redirect('dashboard');
        }

        $this->viewBare('auth/register', ['titulo' => 'Registar']);
    }

    /**
     * Termina a sessão.
     * Rota: /auth/logout
     */
    public function logout(): void
    {
        Auth::logout();
        $this->redirect('auth/login');
    }
}
