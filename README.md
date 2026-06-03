# AutoIRS

Sistema PHP MVC para **gestão de IRS** e **abertura de atividade**, destinado a contabilistas.

- **Domínio:** autoirs.com
- **Alojamento:** Hostinger Business
- **Stack:** PHP 8, PDO/MySQL, Bootstrap 5, dompdf

---

## Funcionalidades

- **Autenticação** de contabilistas (registo/login, password bcrypt, sessões, CSRF, logout).
- **Gestão de clientes** (CRUD): NIF, nome, email, morada, contacto — cada cliente pertence a um contabilista.
- **Cálculo de IRS (Anexo A)** com escalões 2025, dedução específica (4104 €) e deduções à coleta (saúde, educação, gerais, habitação). Cada cálculo é guardado como declaração (detalhe em JSON).
- **Abertura de atividade**: fluxo de estados (`rascunho → dados_recolhidos → guia_gerado → aguarda_validacao → concluido/rejeitado`), upload de comprovativo, geração de **PDF** ("Guia Personalizado") e validação pelo contabilista.
- **Dashboard** com lista de clientes, atalhos por cliente e alertas (estrutura preparada para cron jobs).

---

## Estrutura do projeto

```
autoirs/
├── public/                 # DOCUMENT ROOT (apontar o domínio para aqui)
│   ├── index.php           # front controller
│   ├── .htaccess           # reescrita de URLs
│   └── assets/             # css, js
├── app/
│   ├── controllers/        # AuthController, ClienteController, IrsController, AberturaController, DashboardController
│   ├── models/             # User, Cliente, TabelaIrs, Declaracao, ProcessoAbertura
│   ├── views/              # vistas (auth, clientes, irs, abertura, dashboard, layouts)
│   └── core/               # App (router), Controller, Database, Auth, Csrf, IrsCalculator, PdfService, helpers
├── config/
│   ├── config.php          # configuração geral (sem credenciais)
│   └── database.example.php# modelo de credenciais → copiar para database.php
├── sql/
│   └── estrutura.sql       # criação de tabelas + escalões 2025
├── uploads/                # comprovativos e PDFs (fora da web; servidos por controlador)
├── composer.json           # dependência dompdf
└── README.md
```

---

## Instalação local (desenvolvimento)

> Requer PHP 8+, Composer e MySQL/MariaDB.

1. **Instalar dependências (dompdf):**
   ```bash
   composer install
   ```

2. **Criar a base de dados e importar a estrutura:**
   ```bash
   mysql -u root -p -e "CREATE DATABASE autoirs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p autoirs < sql/estrutura.sql
   ```

3. **Configurar credenciais:**
   ```bash
   cp config/database.example.php config/database.php
   # editar config/database.php com host/dbname/user/pass
   ```
   Em `config/config.php`, durante o desenvolvimento defina:
   ```php
   define('APP_ENV', 'development');
   define('BASE_URL', 'http://localhost:8000');
   ```

4. **Servir a aplicação (usando o front controller como router):**
   ```bash
   php -S localhost:8000 public/index.php
   ```
   Aceda a <http://localhost:8000> e registe a primeira conta de contabilista.

---

## Deploy na Hostinger Business

A aplicação foi pensada para ser publicada via **GitHub Desktop → GitHub → Hostinger**.

### A) Publicar o código no GitHub (GitHub Desktop)

1. No GitHub Desktop: **File → Add local repository** e escolha a pasta `autoirs`
   (ou **File → New repository** apontando para esta pasta).
2. Faça o **commit** inicial e **Publish repository** (pode ser privado).
   > O `.gitignore` já exclui `config/database.php`, `/vendor/` e os ficheiros de `/uploads/`.

### B) Configurar o domínio e a base de dados (hPanel)

1. **Base de dados:** hPanel → *Bases de Dados → MySQL*. Crie a base e o utilizador, e importe `sql/estrutura.sql` pelo **phpMyAdmin**.
2. **Document root:** aponte `autoirs.com` para a pasta `public/` do projeto
   (hPanel → *Websites → Domínios* ou *Avançado → Document Root*).
   - Se não conseguir alterar o document root, o `.htaccess` da raiz reencaminha o tráfego para `/public` e bloqueia `config/`, `app/`, `sql/`.

### C) Colocar o código no servidor

**Opção 1 — Git no hPanel (recomendado):**
- hPanel → *Avançado → Git* → criar repositório a partir do URL do GitHub, definindo o diretório de destino (ex.: `domains/autoirs.com`).
- Para atualizar: botão **Pull** (ou configurar auto-deploy por webhook).

**Opção 2 — SSH (porta 65002):**
```bash
ssh -p 65002 u788472657@us-bos-web1456.main-hosting.eu
cd ~/domains/autoirs.com
git clone git@github.com:SEU_UTILIZADOR/autoirs.git .
```
> A chave pública SSH já está associada à conta. Para atualizações futuras: `git pull`.

### D) Passos finais no servidor

1. **Composer** (dompdf):
   ```bash
   cd ~/domains/autoirs.com
   composer install --no-dev --optimize-autoloader
   ```
   > Se o Composer não estiver disponível por SSH, faça `composer install` localmente e
   > envie a pasta `vendor/` por File Manager/FTP (pode editar o `.gitignore` para a incluir).

2. **Credenciais:** crie `config/database.php` (a partir de `database.example.php`) com os dados da BD da Hostinger.

3. **Produção:** em `config/config.php` confirme:
   ```php
   define('APP_ENV', 'production');
   define('BASE_URL', 'https://autoirs.com');
   ```

4. **Permissões:** garanta que `uploads/` (e `uploads/comprovativos`, `uploads/pdfs`) têm escrita (755/775).

5. Aceda a <https://autoirs.com> e **registe a primeira conta**.

---

## Segurança implementada

- **PDO + prepared statements** em todas as queries (anti-SQL Injection).
- **Password hashing** com bcrypt (`password_hash` / `password_verify`).
- **Tokens CSRF** em todos os formulários POST (`hash_equals`).
- **Escape de output** com `htmlspecialchars` (helper `e()`) — proteção XSS.
- **Sessões** com `httponly`, `samesite=Lax`, `secure` em produção e `session_regenerate_id` no login.
- **Uploads** fora do document root, com validação de tipo/tamanho, nomes aleatórios e `.htaccess` que bloqueia execução/acesso direto.
- Isolamento por contabilista: todas as queries de clientes/processos filtram por `user_id`.

---

## Cron jobs (futuro)

A estrutura do dashboard já contempla **alertas de prazos**. Para automatizar
(ex.: lembretes de entrega de IRS), crie um script em `app/` invocável por CLI e
agende-o em hPanel → *Avançado → Cron Jobs*.

---

## Aviso legal

Os cálculos de IRS são **simplificados** e servem de apoio ao contabilista.
Não substituem a liquidação oficial da Autoridade Tributária.
