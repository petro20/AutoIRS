-- =====================================================================
--  AutoIRS — Estrutura da base de dados
--  Sistema de gestão de IRS e Abertura de Atividade
--
--  Importar via phpMyAdmin (Hostinger) ou:
--    mysql -u UTILIZADOR -p NOME_BD < sql/estrutura.sql
--
--  Motor: InnoDB (suporte a foreign keys) · Charset: utf8mb4
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
--  Tabela: users (contabilistas)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome`          VARCHAR(150) NOT NULL,
    `email`         VARCHAR(190) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Tabela: clientes (pertencem a um contabilista)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `nif`        VARCHAR(9)   NOT NULL,
    `nome`       VARCHAR(190) NOT NULL,
    `email`      VARCHAR(190) DEFAULT NULL,
    `morada`     VARCHAR(255) DEFAULT NULL,
    `contacto`   VARCHAR(50)  DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_clientes_user` (`user_id`),
    CONSTRAINT `fk_clientes_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Tabela: tabelas_irs (escalões por ano)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tabelas_irs` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ano`             SMALLINT UNSIGNED NOT NULL,
    `limite_inferior` DECIMAL(10,2) NOT NULL,
    `limite_superior` DECIMAL(10,2) DEFAULT NULL,   -- NULL no último escalão
    `taxa`            DECIMAL(5,4)  NOT NULL,        -- ex.: 0.1450 = 14,5%
    `parcela_abater`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (`id`),
    KEY `idx_tabelas_ano` (`ano`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Tabela: declaracoes (resultados de cálculo de IRS)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `declaracoes` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id`    INT UNSIGNED NOT NULL,
    `ano`           SMALLINT UNSIGNED NOT NULL,
    `imposto_final` DECIMAL(10,2) NOT NULL,
    `detalhes`      JSON NOT NULL,                   -- estrutura completa do cálculo
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_decl_cliente` (`cliente_id`),
    CONSTRAINT `fk_decl_cliente`
        FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Tabela: processos_abertura (abertura de atividade)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `processos_abertura` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `cliente_id`        INT UNSIGNED NOT NULL,
    `dados`             JSON NOT NULL,               -- dados do formulário
    `estado`            ENUM('rascunho','dados_recolhidos','guia_gerado',
                             'aguarda_validacao','concluido','rejeitado')
                        NOT NULL DEFAULT 'rascunho',
    `comprovativo_path` VARCHAR(255) DEFAULT NULL,
    `pdf_path`          VARCHAR(255) DEFAULT NULL,
    `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_proc_cliente` (`cliente_id`),
    CONSTRAINT `fk_proc_cliente`
        FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
--  DADOS INICIAIS — Escalões de IRS 2025 (Continente)
--  taxa em fração decimal (0.1450 = 14,5%)
-- =====================================================================
INSERT INTO `tabelas_irs` (`ano`, `limite_inferior`, `limite_superior`, `taxa`, `parcela_abater`) VALUES
    (2025,     0.00,  7479.00, 0.1450,    0.00),
    (2025,  7479.00, 11284.00, 0.2100,  486.51),
    (2025, 11284.00, 15992.00, 0.2650, 1108.25),
    (2025, 15992.00, 20700.00, 0.3200, 1987.13),
    (2025, 20700.00, 26355.00, 0.3550, 2709.88),
    (2025, 26355.00, 38632.00, 0.4350, 4842.74),
    (2025, 38632.00, 50483.00, 0.4500, 6044.43),
    (2025, 50483.00,     NULL, 0.4800, 7558.43);
