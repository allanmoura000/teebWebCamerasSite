-- Adiciona a coluna created_at à tabela comentarios
ALTER TABLE comentarios 
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Atualiza registros existentes para ter o mesmo valor de data_hora
UPDATE comentarios 
SET created_at = data_hora 
WHERE created_at IS NULL; 