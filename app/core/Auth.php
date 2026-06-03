<?php
namespace App\Core;

/**
 * Gestão de autenticação baseada em sessões PHP.
 *
 * Guarda apenas o id e nome do contabilista na sessão. A verificação de
 * password é feita no modelo User com password_verify (bcrypt).
 */
class Auth
{
    /**
     * Marca o utilizador como autenticado, regenerando o id de sessão
     * para mitigar session fixation.
     */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int) $user['id'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_email'] = $user['email'];
    }

    /**
     * Termina a sessão do utilizador.
     */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    /**
     * Indica se existe um utilizador autenticado.
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Devolve o id do contabilista autenticado (ou null).
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Devolve o nome do contabilista autenticado.
     */
    public static function nome(): ?string
    {
        return $_SESSION['user_nome'] ?? null;
    }

    /**
     * Protege uma página: redireciona para o login se não autenticado.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }
}
