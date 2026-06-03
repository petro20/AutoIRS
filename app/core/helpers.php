<?php
/**
 * Funções auxiliares globais.
 */

if (!function_exists('e')) {
    /**
     * Escapa texto para saída em HTML (proteção contra XSS).
     */
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('eur')) {
    /**
     * Formata um valor monetário em euros (formato português).
     */
    function eur($value): string
    {
        return number_format((float) $value, 2, ',', '.') . ' €';
    }
}

if (!function_exists('url')) {
    /**
     * Constrói um URL absoluto a partir da BASE_URL.
     */
    function url(string $path = ''): string
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('t')) {
    /**
     * Traduz uma chave para o idioma atual (i18n).
     */
    function t(string $key, ?string $default = null): string
    {
        return \App\Core\I18n::t($key, $default);
    }
}

if (!function_exists('old')) {
    /**
     * Devolve um valor previamente submetido (re-preenchimento de formulários).
     */
    function old(string $key, $default = ''): string
    {
        return e($_SESSION['old'][$key] ?? $default);
    }
}
