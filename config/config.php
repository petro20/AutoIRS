<?php
/**
 * Configuração global da aplicação AutoIRS.
 *
 * Define constantes usadas em toda a aplicação (URL base, caminhos,
 * dedução específica do IRS, etc.). Não contém credenciais — essas
 * estão em /config/database.php (ver database.example.php).
 */

// --- Ambiente -------------------------------------------------------------
// 'production' em produção (Hostinger). Em 'development' mostra erros.
define('APP_ENV', 'production');

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// --- Identidade da aplicação ---------------------------------------------
define('APP_NAME', 'AutoIRS');

/**
 * URL base do site.
 * Em produção: 'https://autoirs.com'
 * Em ambiente local com a pasta /public como document root deixe vazio.
 * Como o document root aponta para /public, a base é a raiz do domínio.
 */
define('BASE_URL', 'https://autoirs.com');

// --- Caminhos do sistema de ficheiros ------------------------------------
// Raiz do projeto (um nível acima de /config).
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// --- Parâmetros fiscais (IRS 2025) ---------------------------------------
// Dedução específica da categoria A (trabalho dependente) para 2025.
define('DEDUCAO_ESPECIFICA', 4104.00);

// --- Upload --------------------------------------------------------------
// Tamanho máximo de comprovativos (5 MB) e extensões permitidas.
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
define('UPLOAD_ALLOWED_EXT', ['pdf', 'jpg', 'jpeg', 'png']);
