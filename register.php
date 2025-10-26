<?php
require_once 'config.php';
iniciarSessao();

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = conectarBanco();

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $cidade = trim($_POST['cidade'] ?? '');
        $data_nascimento = $_POST['data_nascimento'] ?? '';
        $tipo_participante = $_POST['tipo_participante'] ?? '';
        
        // 1. Validação de campos
        if (empty($nome) || empty($email) || empty($senha) || empty($cidade) || empty($data_nascimento) || empty($tipo_participante)) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato de e-mail inválido.");
        }
        
        if (!in_array($tipo_participante, ['interno', 'externo'])) {
            throw new Exception("Tipo de participante inválido.");
        }

        // 2. Verifica se o e-mail já existe
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Este e-mail já está cadastrado.");
        }

        // 3. Hash da senha (segurança!)
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // 4. Insere novo usuário
        $sql = "INSERT INTO usuarios (nome, email, senha, cidade, data_nascimento, tipo_participante) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senha_hash, $cidade, $data_nascimento, $tipo_participante]);

        $mensagem = "Cadastro realizado com sucesso! Você pode fazer login agora.";
        $tipo_mensagem = "success";
        
        // Redireciona para login após o sucesso
        header('Location: login.php?sucesso=cadastro');
        exit;

    } catch (Exception $e) {
        $mensagem = "Erro no cadastro: " . $e->getMessage();
        $tipo_mensagem = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine IFMG - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Adicionando um estilo básico para o formulário de registro */
        .register-form {
            background-color: #1a1a1a;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5 );
            color: #fff;
        }
        .register-form h1 {
            color: #39FF14; /* Verde Neon */
            text-shadow: 0 0 5px #39FF14;
        }
    </style>
</head>
<body>
      <a href="login.php" class="back-arrow" title="Voltar ao Login">
        <!-- Substitua a imagem por um ícone ou remova se não tiver a imagem -->
        <i class="fas fa-arrow-left fa-2x text-white" style="position:fixed; top:20px; left:20px;"></i>
    </a>
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row h-100 w-100">
          <div class="col-lg-6 d-flex flex-column justify-content-center p-5 h-100">
                <div class="register-form">
                    <h1 class="display-4 fw-bold mb-4 text-white">REGISTRE-SE</h1>
                    <p class="mb-4">
                        Já tem uma conta? 
                        <a href="login.php" class="text-success">Faça login</a>
                    </p>
                    
                    <!-- Mensagem de feedback -->
                    <?php if ($mensagem): ?>
                        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                            <?php echo $mensagem; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" name="nome" placeholder="Nome" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-lg" name="email" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control form-control-lg" name="senha" placeholder="Senha" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" name="cidade" placeholder="Cidade" required>
                        </div>
                        <div class="mb-3">
                            <input type="date" class="form-control form-control-lg" name="data_nascimento" required>
                        </div>
                        <div class="mb-4">
                            <label class="text-white text-decoration-none mb-2">Tipo de participante</label>
                            <select class="form-select form-select-lg" name="tipo_participante" required>
                                <option value="interno">Interno</option>
                                <option value="externo">Externo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Registrar</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-0 h-100">
              <img src="img/campus-ifmg3.jpg" alt="IFMG Estudantes"
                style="width: 100%; height: 100vh; object-fit: cover;">
            </div>
          </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
