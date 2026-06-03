<?php
namespace App\Core;

/**
 * Internacionalização (i18n).
 *
 * Idiomas suportados: os mais falados em Portugal (PT nativo + EN/FR/ES).
 * O idioma escolhido é guardado na sessão. As traduções estão em /app/lang.
 */
class I18n
{
    /** Idiomas suportados (código => nome nativo). */
    public const SUPPORTED = [
        'pt'   => 'Português',
        'ptbr' => 'Português (Brasil)',
        'mwl'  => 'Mirandês',
        'en'   => 'English',
        'fr'   => 'Français',
        'es'   => 'Español',
    ];

    /** Bandeira (emoji) por idioma, para o seletor. */
    public const FLAGS = [
        'pt'   => '🇵🇹',
        'ptbr' => '🇧🇷',
        'mwl'  => '🗣️',
        'en'   => '🇬🇧',
        'fr'   => '🇫🇷',
        'es'   => '🇪🇸',
    ];

    private const DEFAULT = 'pt';

    private static string $lang = self::DEFAULT;
    private static array $messages = [];

    /**
     * Inicializa o idioma a partir da sessão (com fallback para PT).
     * Deve ser chamado no arranque, após session_start().
     */
    public static function init(): void
    {
        $lang = $_SESSION['lang'] ?? self::DEFAULT;
        if (!isset(self::SUPPORTED[$lang])) {
            $lang = self::DEFAULT;
        }
        self::$lang = $lang;

        // Carrega as traduções do idioma escolhido.
        $file = APP_PATH . '/lang/' . $lang . '.php';
        self::$messages = is_file($file) ? require $file : [];

        // Fallback: preenche chaves em falta com o PT (união de arrays).
        if ($lang !== self::DEFAULT) {
            $base = APP_PATH . '/lang/' . self::DEFAULT . '.php';
            if (is_file($base)) {
                self::$messages += require $base;
            }
        }
    }

    /**
     * Traduz uma chave. Se não existir, devolve o $default ou a própria chave.
     */
    public static function t(string $key, ?string $default = null): string
    {
        return self::$messages[$key] ?? ($default ?? $key);
    }

    /** Idioma atual (ex.: 'pt'). */
    public static function lang(): string
    {
        return self::$lang;
    }

    /** Define o idioma (validado) e guarda na sessão. */
    public static function set(string $lang): void
    {
        if (isset(self::SUPPORTED[$lang])) {
            $_SESSION['lang'] = $lang;
            self::$lang = $lang;
        }
    }
}
