-- Cria a tabela usuarios se ela não existir
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `codigo_verificacao` varchar(6) DEFAULT NULL,
  `data_envio_codigo` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `codigo_verificacao` (`codigo_verificacao`),
  KEY `data_envio_codigo` (`data_envio_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para cadastro de usuários
CREATE TABLE IF NOT EXISTS `cadastro_simples` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `codigo_verificacao` varchar(6) DEFAULT NULL,
  `data_envio_codigo` datetime DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cpf` (`cpf`),
  KEY `codigo_verificacao` (`codigo_verificacao`),
  KEY `data_envio_codigo` (`data_envio_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Adiciona as colunas se não existirem
SET @dbname = 'estagio_cameras';
SET @tablename = 'cadastro_simples';
SET @columnname = 'codigo_verificacao';
SET @columntype = 'varchar(6)';
SET @columnposition = 'AFTER phone';

SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ),
    'SELECT "Column already exists"',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype, ' DEFAULT NULL ', @columnposition)
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'data_envio_codigo';
SET @columntype = 'datetime';
SET @columnposition = 'AFTER codigo_verificacao';

SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND COLUMN_NAME = @columnname
    ),
    'SELECT "Column already exists"',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype, ' DEFAULT NULL ', @columnposition)
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adiciona os índices se não existirem
SET @indexname = 'idx_codigo_verificacao';
SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.STATISTICS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND INDEX_NAME = @indexname
    ),
    'SELECT "Index already exists"',
    CONCAT('CREATE INDEX ', @indexname, ' ON ', @tablename, ' (codigo_verificacao)')
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @indexname = 'idx_data_envio_codigo';
SET @query = IF(
    EXISTS(
        SELECT * FROM INFORMATION_SCHEMA.STATISTICS 
        WHERE TABLE_SCHEMA = @dbname
        AND TABLE_NAME = @tablename
        AND INDEX_NAME = @indexname
    ),
    'SELECT "Index already exists"',
    CONCAT('CREATE INDEX ', @indexname, ' ON ', @tablename, ' (data_envio_codigo)')
);
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt; 