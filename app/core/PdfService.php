<?php
namespace App\Core;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Serviço de geração de PDF com dompdf.
 *
 * Renderiza uma vista PHP para HTML e converte-a em PDF, gravando o ficheiro
 * em /uploads/pdfs. Requer a instalação via Composer (dompdf/dompdf).
 */
class PdfService
{
    /**
     * Gera um PDF a partir de uma vista e devolve o caminho relativo gravado.
     *
     * @param string $view     Vista relativa a /app/views (ex.: 'abertura/guia_pdf')
     * @param array  $data     Variáveis para a vista.
     * @param string $fileName Nome do ficheiro a gravar (sem caminho).
     * @return string Caminho relativo do PDF (ex.: 'pdfs/guia_5.pdf').
     */
    public static function gerar(string $view, array $data, string $fileName): string
    {
        // 1. Renderizar a vista para uma string HTML.
        $html = self::render(APP_PATH . '/views/' . $view . '.php', $data);

        // 2. Configurar e executar o dompdf.
        $options = new Options();
        $options->set('isRemoteEnabled', false);   // não carregar recursos externos
        $options->set('defaultFont', 'DejaVu Sans'); // suporte a acentos

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 3. Gravar em /uploads/pdfs.
        $dir = UPLOADS_PATH . '/pdfs';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $fullPath = $dir . '/' . $fileName;
        file_put_contents($fullPath, $dompdf->output());

        return 'pdfs/' . $fileName;
    }

    /**
     * Renderiza um ficheiro PHP para string (output buffering).
     */
    private static function render(string $file, array $data): string
    {
        extract($data);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
