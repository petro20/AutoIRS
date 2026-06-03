<?php
namespace App\Core;

/**
 * Front controller / router.
 *
 * Interpreta o URL no formato:  /controlador/metodo/param1/param2
 * Ex.:  /cliente/edit/5  ->  ClienteController::edit(5)
 *
 * Por omissão usa DashboardController::index.
 */
class App
{
    private string $controllerName = 'DashboardController';
    private string $method = 'index';
    private array $params = [];

    public function run(): void
    {
        $url = $this->parseUrl();

        // 1) Determinar controlador.
        if (!empty($url[0])) {
            $candidate = ucfirst($url[0]) . 'Controller';
            if (file_exists(APP_PATH . '/controllers/' . $candidate . '.php')) {
                $this->controllerName = $candidate;
                array_shift($url);
            }
        }

        $controllerClass = 'App\\Controllers\\' . $this->controllerName;
        $controller = new $controllerClass();

        // 2) Determinar método.
        if (!empty($url[0])) {
            if (method_exists($controller, $url[0])) {
                $this->method = $url[0];
                array_shift($url);
            } else {
                http_response_code(404);
                die('Página não encontrada.');
            }
        }

        // 3) Parâmetros restantes.
        $this->params = $url ? array_values($url) : [];

        // 4) Despacho.
        call_user_func_array([$controller, $this->method], $this->params);
    }

    /**
     * Divide o caminho do pedido em segmentos, ignorando query string.
     *
     * Fontes (por ordem de prioridade):
     *   1. ?url=...  -> preenchido pela reescrita .htaccess (Apache/Hostinger);
     *   2. REQUEST_URI -> fallback para o servidor embutido do PHP e ambientes
     *      sem mod_rewrite.
     */
    private function parseUrl(): array
    {
        $url = $_GET['url'] ?? '';

        // Fallback: derivar o caminho a partir do REQUEST_URI.
        if ($url === '') {
            $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
            // Remover o nome do script da base, se presente (ex.: /index.php).
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            if ($base !== '' && strpos($uri, $base) === 0) {
                $uri = substr($uri, strlen($base));
            }
            $url = trim($uri, '/');
            // Ignorar o próprio front controller no caminho.
            if ($url === 'index.php') {
                $url = '';
            }
        }

        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url === '' ? [] : explode('/', $url);
    }
}
