<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\PdfService;
use App\Models\Cliente;
use App\Models\ProcessoAbertura;

/**
 * Módulo de Abertura de Atividade.
 *
 * Gere o ciclo de vida de um processo de início de atividade:
 *   rascunho -> dados_recolhidos -> guia_gerado -> aguarda_validacao
 *            -> concluido | rejeitado
 */
class AberturaController extends Controller
{
    /** Lista de CAE comuns (código => descrição). */
    private const CAE = [
        '62010' => '62010 — Programação informática',
        '62020' => '62020 — Consultoria em informática',
        '63110' => '63110 — Atividades de processamento de dados, domiciliação de informação',
        '69200' => '69200 — Contabilidade, auditoria e consultoria fiscal',
        '70220' => '70220 — Consultoria para os negócios e gestão',
        '74100' => '74100 — Atividades de design',
        '47910' => '47910 — Comércio a retalho por correspondência ou via Internet',
        '56101' => '56101 — Restaurantes tipo tradicional',
        'outro' => 'Outro (especificar nas observações)',
    ];

    public function __construct()
    {
        Auth::requireLogin();
    }

    /**
     * Lista os processos de um cliente e mostra o formulário de novo processo.
     * Rota: /abertura/index/{cliente_id}
     */
    public function index($clienteId = null): void
    {
        $cliente = $this->clienteOuFalha((int) $clienteId);

        $this->view('abertura/index', [
            'titulo'    => 'Abertura de atividade — ' . $cliente['nome'],
            'cliente'   => $cliente,
            'processos' => ProcessoAbertura::allByCliente((int) $cliente['id']),
        ]);
    }

    /**
     * Formulário de recolha de dados (GET) e gravação (POST).
     * Rota: /abertura/create/{cliente_id}
     */
    public function create($clienteId = null): void
    {
        $cliente = $this->clienteOuFalha((int) $clienteId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::check();

            // --- Validação dos campos ------------------------------------
            $dados = [
                'data_inicio'              => trim($_POST['data_inicio'] ?? ''),
                'cae'                      => trim($_POST['cae'] ?? ''),
                'volume_negocios_estimado' => (float) str_replace(',', '.', $_POST['volume_negocios_estimado'] ?? '0'),
                'iban'                     => strtoupper(str_replace(' ', '', $_POST['iban'] ?? '')),
                'opcao_iva'                => trim($_POST['opcao_iva'] ?? ''),
                'observacoes'              => trim($_POST['observacoes'] ?? ''),
            ];

            $erros = [];
            if ($dados['data_inicio'] === '') {
                $erros[] = 'A data de início é obrigatória.';
            }
            if (!isset(self::CAE[$dados['cae']]) && $dados['cae'] !== '') {
                // aceitamos qualquer CAE numérico (campo "outro")
                if (!preg_match('/^\d{5}$/', $dados['cae'])) {
                    $erros[] = 'CAE inválido.';
                }
            }
            if (!preg_match('/^PT\d{23}$/', $dados['iban'])) {
                $erros[] = 'IBAN inválido (formato esperado: PT seguido de 23 dígitos).';
            }

            // --- Upload do comprovativo (opcional) -----------------------
            $comprovativoPath = $this->processarUpload('comprovativo', $erros);

            if ($erros) {
                $this->flash('danger', implode(' ', $erros));
                $_SESSION['old'] = $_POST;
                $this->redirect('abertura/create/' . $cliente['id']);
            }

            if ($comprovativoPath !== null) {
                $dados['comprovativo_path'] = $comprovativoPath;
            }

            // Estado inicial: dados recolhidos.
            ProcessoAbertura::create((int) $cliente['id'], $dados, 'dados_recolhidos');
            unset($_SESSION['old']);
            $this->flash('success', 'Dados recolhidos. Já pode gerar o guia.');
            $this->redirect('abertura/index/' . $cliente['id']);
        }

        $this->view('abertura/create', [
            'titulo'   => 'Recolher dados — ' . $cliente['nome'],
            'cliente'  => $cliente,
            'caeLista' => self::CAE,
        ]);
    }

    /**
     * Mostra o detalhe de um processo.
     * Rota: /abertura/show/{id}
     */
    public function show($id = null): void
    {
        [$processo, $cliente] = $this->processoOuFalha((int) $id);

        $this->view('abertura/show', [
            'titulo'   => 'Processo de abertura #' . $processo['id'],
            'processo' => $processo,
            'cliente'  => $cliente,
            'dados'    => json_decode($processo['dados'], true) ?: [],
            'labels'   => ProcessoAbertura::ESTADO_LABELS,
        ]);
    }

    /**
     * Gera o PDF "Guia Personalizado para Abertura de Atividade".
     * Rota: /abertura/gerarGuia/{id}  (POST)
     */
    public function gerarGuia($id = null): void
    {
        Csrf::check();
        [$processo, $cliente] = $this->processoOuFalha((int) $id);
        $dados = json_decode($processo['dados'], true) ?: [];

        try {
            $pdfPath = PdfService::gerar('abertura/guia_pdf', [
                'cliente'  => $cliente,
                'dados'    => $dados,
                'processo' => $processo,
                'caeLista' => self::CAE,
            ], 'guia_abertura_' . $processo['id'] . '.pdf');

            ProcessoAbertura::setPdf((int) $processo['id'], $pdfPath);
            $this->flash('success', 'Guia gerado com sucesso.');
        } catch (\Throwable $e) {
            $msg = APP_ENV === 'development' ? $e->getMessage() : 'Verifique se o dompdf está instalado (composer install).';
            $this->flash('danger', 'Erro ao gerar o PDF. ' . $msg);
        }

        $this->redirect('abertura/show/' . $processo['id']);
    }

    /**
     * Faz o download do PDF gerado.
     * Rota: /abertura/download/{id}
     */
    public function download($id = null): void
    {
        [$processo, ] = $this->processoOuFalha((int) $id);
        if (empty($processo['pdf_path'])) {
            $this->flash('danger', 'Ainda não existe PDF para este processo.');
            $this->redirect('abertura/show/' . $processo['id']);
        }

        $full = UPLOADS_PATH . '/' . $processo['pdf_path'];
        if (!file_exists($full)) {
            $this->flash('danger', 'Ficheiro não encontrado.');
            $this->redirect('abertura/show/' . $processo['id']);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($full) . '"');
        header('Content-Length: ' . filesize($full));
        readfile($full);
        exit;
    }

    /**
     * Serve o comprovativo carregado (rota autenticada, pois /uploads fica
     * fora do document root /public).
     * Rota: /abertura/comprovativo/{id}
     */
    public function comprovativo($id = null): void
    {
        [$processo, ] = $this->processoOuFalha((int) $id);
        if (empty($processo['comprovativo_path'])) {
            $this->flash('danger', 'Este processo não tem comprovativo.');
            $this->redirect('abertura/show/' . $processo['id']);
        }

        $full = UPLOADS_PATH . '/' . $processo['comprovativo_path'];
        if (!file_exists($full)) {
            $this->flash('danger', 'Ficheiro não encontrado.');
            $this->redirect('abertura/show/' . $processo['id']);
        }

        // Determina o tipo de conteúdo a partir da extensão.
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        $mime = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg',
                 'jpeg' => 'image/jpeg', 'png' => 'image/png'][$ext] ?? 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . basename($full) . '"');
        header('Content-Length: ' . filesize($full));
        readfile($full);
        exit;
    }

    /**
     * Envia o processo para validação do contabilista.
     * Rota: /abertura/enviarValidacao/{id}  (POST)
     */
    public function enviarValidacao($id = null): void
    {
        Csrf::check();
        [$processo, ] = $this->processoOuFalha((int) $id);
        ProcessoAbertura::updateEstado((int) $processo['id'], 'aguarda_validacao');
        $this->flash('info', 'Processo enviado para validação.');
        $this->redirect('abertura/show/' . $processo['id']);
    }

    /**
     * Aprova o processo (conclui).
     * Rota: /abertura/aprovar/{id}  (POST)
     */
    public function aprovar($id = null): void
    {
        Csrf::check();
        [$processo, ] = $this->processoOuFalha((int) $id);
        ProcessoAbertura::updateEstado((int) $processo['id'], 'concluido');
        $this->flash('success', 'Processo concluído.');
        $this->redirect('abertura/show/' . $processo['id']);
    }

    /**
     * Rejeita o processo.
     * Rota: /abertura/rejeitar/{id}  (POST)
     */
    public function rejeitar($id = null): void
    {
        Csrf::check();
        [$processo, ] = $this->processoOuFalha((int) $id);
        ProcessoAbertura::updateEstado((int) $processo['id'], 'rejeitado');
        $this->flash('warning', 'Processo rejeitado.');
        $this->redirect('abertura/show/' . $processo['id']);
    }

    // ---------------------------------------------------------------------
    // Métodos auxiliares
    // ---------------------------------------------------------------------

    /**
     * Obtém um cliente do contabilista ou redireciona com erro.
     */
    private function clienteOuFalha(int $clienteId): array
    {
        $cliente = Cliente::find($clienteId, Auth::id());
        if (!$cliente) {
            $this->flash('danger', 'Cliente não encontrado.');
            $this->redirect('dashboard');
        }
        return $cliente;
    }

    /**
     * Obtém um processo + cliente, validando a posse pelo contabilista.
     *
     * @return array{0: array, 1: array} [processo, cliente]
     */
    private function processoOuFalha(int $id): array
    {
        $processo = ProcessoAbertura::find($id);
        if (!$processo) {
            $this->flash('danger', 'Processo não encontrado.');
            $this->redirect('dashboard');
        }
        $cliente = Cliente::find((int) $processo['cliente_id'], Auth::id());
        if (!$cliente) {
            $this->flash('danger', 'Sem permissão para aceder a este processo.');
            $this->redirect('dashboard');
        }
        return [$processo, $cliente];
    }

    /**
     * Processa o upload de um ficheiro com validação de tipo e tamanho.
     *
     * @param string $campo Nome do campo <input type="file">.
     * @param array  $erros Recebe mensagens de erro por referência.
     * @return string|null Caminho relativo gravado (ex.: 'comprovativos/abc.pdf') ou null.
     */
    private function processarUpload(string $campo, array &$erros): ?string
    {
        if (empty($_FILES[$campo]['name'])) {
            return null; // upload opcional
        }

        $file = $_FILES[$campo];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $erros[] = 'Erro no upload do comprovativo.';
            return null;
        }
        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $erros[] = 'O comprovativo excede o tamanho máximo permitido (5 MB).';
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, UPLOAD_ALLOWED_EXT, true)) {
            $erros[] = 'Tipo de ficheiro não permitido (apenas PDF, JPG, PNG).';
            return null;
        }

        // Nome único e seguro para evitar colisões e path traversal.
        $nome = bin2hex(random_bytes(16)) . '.' . $ext;
        $dir = UPLOADS_PATH . '/comprovativos';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $nome)) {
            $erros[] = 'Não foi possível guardar o comprovativo.';
            return null;
        }

        return 'comprovativos/' . $nome;
    }
}
