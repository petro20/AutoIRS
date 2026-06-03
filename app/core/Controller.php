<?php
namespace App\Core;

/**
 * Controlador base. Todos os controladores estendem esta classe.
 *
 * Fornece utilitários comuns: carregamento de vistas com layout,
 * redirecionamentos e gestão de mensagens flash.
 */
abstract class Controller
{
    /**
     * Renderiza uma vista dentro do layout (header + footer).
     *
     * @param string $view  Caminho da vista relativo a /app/views (ex.: 'clientes/index')
     * @param array  $data  Variáveis disponibilizadas à vista.
     */
    protected function view(string $view, array $data = []): void
    {
        // Extrai o array em variáveis individuais ($data['x'] => $x).
        extract($data);

        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die('Vista não encontrada: ' . htmlspecialchars($view));
        }

        require APP_PATH . '/views/layouts/header.php';
        require $viewFile;
        require APP_PATH . '/views/layouts/footer.php';
    }

    /**
     * Renderiza uma vista sem layout (ex.: páginas de login).
     */
    protected function viewBare(string $view, array $data = []): void
    {
        extract($data);
        require APP_PATH . '/views/' . $view . '.php';
    }

    /**
     * Redireciona para um caminho relativo à BASE_URL e termina.
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Guarda uma mensagem flash (mostrada uma única vez na próxima página).
     *
     * @param string $type  'success' | 'danger' | 'warning' | 'info'
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}
