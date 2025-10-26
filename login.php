<?php
require_once 'config.php';
iniciarSessao();

$mensagem = '';
$tipo_mensagem = '';

// Trata mensagens de feedback (sucesso/erro)
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'cadastro') {
    $mensagem = 'Cadastro realizado com sucesso! Faça login para continuar.';
    $tipo_mensagem = 'success';
} elseif (isset($_GET['sucesso']) && $_GET['sucesso'] == 'logout') {
    $mensagem = 'Você foi desconectado com sucesso.';
    $tipo_mensagem = 'success';
} elseif (isset($_GET['erro']) && $_GET['erro'] == 'nao_logado') {
    $mensagem = 'Acesso negado. Por favor, faça login.';
    $tipo_mensagem = 'danger';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = conectarBanco();

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            throw new Exception("E-mail e senha são obrigatórios.");
        }

        // 1. Busca o usuário pelo e-mail
        $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            throw new Exception("E-mail ou senha incorretos.");
        }

        // 2. Verifica a senha
        if (!password_verify($senha, $usuario['senha'])) {
            throw new Exception("E-mail ou senha incorretos.");
        }

        // 3. Login bem-sucedido: Inicia a sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        
        // Redireciona para a página inicial (ou outra página de acesso)
        header('Location: inicio.php');
        exit;

    } catch (Exception $e) {
        $mensagem = "Erro no login: " . $e->getMessage();
        $tipo_mensagem = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <!-- Cabeçalho / Metadados e CSS -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Sistema de Ações</title>
        
        <!-- Bootstrap / FontAwesome / Lightbox -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">
    
        <!-- Estilo Customizado -->
        <link rel="stylesheet" href="styles.css">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Bungee&family=New+Amsterdam&family=Staatliches&display=swap' );
            /* Estilos do login/cadastro */
            .login-form {
                background-color: #1a1a1a;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
                color: #fff;
            }
            .login-form h1 {
                color: #39FF14; /* Verde Neon */
                text-shadow: 0 0 5px #39FF14;
            }
        </style>
        
    </head>

 <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark  border-secondary">
        <div class="container">
<div class="navbar-brand d-flex align-items-center">
    <a class="nav-link active" href="home.html"><img src="https://uploads.onecompiler.io/43vms4uzs/43vmrkwnq/favicon_white.png" alt="Logo Cine IFMG" style="height:48px;"></a>
</div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="inicio.php">INÍCIO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cineclubes.php">CINECLUBES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sobre.html">SOBRE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="cadAcao.php">CADASTRAR EVENTO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php">ACESSO</a>
                    </li>

                </ul>
                
            </div>
        </div>
    </nav>

 <!-- Login -->
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row h-100 w-100">
            <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center p-5 h-100">
                <div class="login-form small-login-form w-100" style="max-width: 400px;">
                    <h1 class="display-4 fw-bold mb-4">LOGIN</h1>
                    <p class="mb-4">
                        Ainda não tem uma conta? 
                        <a href="register.php" class="text-success">Registre-se</a>
                    </p>
                    
                    <!-- Mensagem de feedback -->
                    <?php if ($mensagem ): ?>
                        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                            <?php echo $mensagem; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

<!-- Formulario -->                  
                    <form method="POST">
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-lg" name="email" placeholder="Email" required>
                        </div>
                        <div class="mb-4">
                            <input type="password" class="form-control form-control-lg" name="senha" placeholder="Senha" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-0 h-100">
                <img src="img/placa upscaled.png" alt="IFMG Campus"
                     style="width: 100%; height: 100vh; object-fit: cover;">
              </div>
        </div>
    </div>

 <!-- Java -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
