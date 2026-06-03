<?php
namespace App\Core;

/**
 * Geração e validação de tokens CSRF.
 *
 * O token é guardado na sessão e injetado num campo oculto em todos os
 * formulários. Em cada submissão POST validamos o token recebido.
 */
class Csrf
{
    /**
     * Devolve o token atual da sessão, gerando-o na primeira utilização.
     */
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            // 32 bytes aleatórios criptograficamente seguros.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Gera o HTML do campo oculto a colocar dentro dos <form>.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida o token recebido por POST contra o da sessão.
     * Usa hash_equals para comparação resistente a timing attacks.
     */
    public static function validate(?string $token): bool
    {
        return !empty($token)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Verifica o token de um pedido POST e termina a execução se for inválido.
     */
    public static function check(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!self::validate($token)) {
                http_response_code(419);
                die('Token CSRF inválido ou expirado. Recarregue a página e tente novamente.');
            }
        }
    }
}
