-- Estrutura do banco de dados para o sistema de ações

USE sistema_acoes;

CREATE TABLE acoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_acao ENUM('filme', 'debate', 'curta', 'palestra') NOT NULL,
    nome_coordenador VARCHAR(255) NOT NULL,
    local_evento VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    data_evento DATE NOT NULL,
    horario_inicial TIME NOT NULL,
    horario_final TIME NOT NULL,
    campus ENUM('RN', 'C', 'IP') NOT NULL,
    foto_capa VARCHAR(255),
    galeria_fotos TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar diretório para upload de imagens
CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho_arquivo VARCHAR(255) NOT NULL,
    acao_id INT,
    tipo ENUM('capa', 'galeria') NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (acao_id) REFERENCES acoes(id) ON DELETE CASCADE
);

