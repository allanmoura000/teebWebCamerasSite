-- Seleciona o banco de dados
USE camerasTeeb2;

-- Verifica se a coluna codigo_verificacao existe
SET @dbname = DATABASE();
SET @tablename = "cadastro_simples";
SET @columnname = "codigo_verificacao";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE 
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE cadastro_simples ADD COLUMN codigo_verificacao VARCHAR(6) DEFAULT NULL"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verifica se a coluna data_envio_codigo existe
SET @columnname = "data_envio_codigo";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE 
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE cadastro_simples ADD COLUMN data_envio_codigo DATETIME DEFAULT NULL"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adiciona índices se não existirem
SET @indexname = "idx_codigo_verificacao";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE 
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (INDEX_NAME = @indexname)
  ) > 0,
  "SELECT 1",
  "CREATE INDEX idx_codigo_verificacao ON cadastro_simples(codigo_verificacao)"
));
PREPARE createIndexIfNotExists FROM @preparedStatement;
EXECUTE createIndexIfNotExists;
DEALLOCATE PREPARE createIndexIfNotExists;

SET @indexname = "idx_data_envio_codigo";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE 
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (INDEX_NAME = @indexname)
  ) > 0,
  "SELECT 1",
  "CREATE INDEX idx_data_envio_codigo ON cadastro_simples(data_envio_codigo)"
));
PREPARE createIndexIfNotExists FROM @preparedStatement;
EXECUTE createIndexIfNotExists;
DEALLOCATE PREPARE createIndexIfNotExists; 