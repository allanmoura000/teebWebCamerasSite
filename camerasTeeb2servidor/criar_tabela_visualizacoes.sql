-- Recria a tabela visualizacoes com a estrutura correta
DROP TABLE IF EXISTS visualizacoes;
CREATE TABLE visualizacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    camera_id INT NOT NULL UNIQUE,
    total INT NOT NULL DEFAULT 0,
    online INT NOT NULL DEFAULT 0,
    ultima_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (camera_id) REFERENCES cad_cameras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices para melhor performance
CREATE INDEX idx_camera_id ON visualizacoes(camera_id);
CREATE INDEX idx_ultima_atualizacao ON visualizacoes(ultima_atualizacao); 