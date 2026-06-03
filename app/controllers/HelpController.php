<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;

/**
 * Página de Ajuda — guia de utilização do sistema.
 */
class HelpController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    /**
     * Rota: /help
     */
    public function index(): void
    {
        $this->view('help/index', ['titulo' => t('help.title')]);
    }
}
