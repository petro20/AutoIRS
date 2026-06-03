<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\I18n;

/**
 * Troca de idioma. Não exige login (funciona também no ecrã de login).
 */
class LangController extends Controller
{
    /**
     * Define o idioma e volta para a página anterior.
     * Rota: /lang/set/{codigo}  (ex.: /lang/set/en)
     */
    public function set($lang = null): void
    {
        I18n::set((string) $lang);

        // Volta para a página de origem, se for do próprio site (evita open redirect).
        $back = $_SERVER['HTTP_REFERER'] ?? '';
        if ($back !== '' && strpos($back, BASE_URL) === 0) {
            header('Location: ' . $back);
            exit;
        }
        $this->redirect('dashboard');
    }
}
