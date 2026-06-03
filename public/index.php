<?php
/**
 * Ponto de entrada único da aplicação (front controller).
 *
 * O document root do alojamento deve apontar para esta pasta /public.
 * Todos os pedidos são reescritos pelo .htaccess para este ficheiro.
 */

declare(strict_types=1);

// --- Servidor embutido do PHP: servir ficheiros estáticos existentes -----
// (Em Apache/Hostinger isto é tratado pelo .htaccess; aqui é só para dev.)
if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path !== '/' && is_file(__DIR__ . $path)) {
        return false; // o servidor embutido serve o ficheiro diretamente
    }
}

// --- Configuração --------------------------------------------------------
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../app/core/helpers.php';

// --- Sessão segura -------------------------------------------------------
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,                              // inacessível por JS (anti-XSS)
    'samesite' => 'Lax',                             // mitiga CSRF
    'secure'   => (APP_ENV === 'production'),        // só HTTPS em produção
]);
session_start();

// --- Autoloaders ---------------------------------------------------------
// 1) Composer (dompdf e dependências), se instalado.
$composer = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($composer)) {
    require $composer;
}

// 2) Autoloader PSR-4 para o namespace App\ -> /app
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
       $relative = substr($class, strlen($prefix)); // ex.: "Core\App"
    $parts = explode('\\', $relative);
    $className = array_pop($parts);              // "App" (mantém a capitalização)
    $dir = strtolower(implode('/', $parts));     // "core" (pasta em minúsculas)
    $file = APP_PATH . '/' . ($dir !== '' ? $dir . '/' : '') . $className . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// --- Arranque da aplicação ----------------------------------------------
use App\Core\App;

$app = new App();
$app->run();
