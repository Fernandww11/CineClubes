<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_acoes');

// Função para iniciar a sessão de forma segura
function iniciarSessao() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Função para conectar ao banco de dados
function conectarBanco() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}


// Função para upload de arquivos
function uploadArquivo($arquivo, $destino) {
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extensao, $extensoesPermitidas)) {
        return false;
    }
    
    $nomeArquivo = uniqid() . '.' . $extensao;
    $caminhoCompleto = $destino . '/' . $nomeArquivo;
    
    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return $nomeArquivo;
    }
    
    return false;
}
?>
