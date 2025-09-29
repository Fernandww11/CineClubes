

<?php
session_start();
require_once 'config.php';

$PASSWORD_HASH = '$2y$10$8yzGZkgQb0U.qFv/UxkKgeekcKqW1xoYUF2U1FxmPuhBGNax/eC0W'; // hash de 'adm123'
if (!isset($_SESSION['tries'])) $_SESSION['tries'] = 0;
$MAX_TRIES = 1000000000000000000000;
$message_senha = 'erro';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha_site'])) {
    if ($_SESSION['tries'] >= $MAX_TRIES) {
        $message_senha = 'Máximo de tentativas atingido.';
    } else {
        if (password_verify($_POST['senha_site'], $PASSWORD_HASH)) {
            $_SESSION['unlocked'] = true;
            $_SESSION['tries'] = 0;
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['tries'] += 1;
            $remaining = $MAX_TRIES - $_SESSION['tries'];
            $message_senha = "Senha incorreta. Tentativas restantes: {$remaining}";
        }
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['unlocked']);
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

$unlocked = !empty($_SESSION['unlocked']);

// --- PROCESSO DE CADASTRO EXISTENTE ---
$mensagem = '';
$tipo_mensagem = '';

if ($unlocked && $_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['senha_site'])) {
    try {
        $pdo = conectarBanco();
        
        $campos_obrigatorios = ['tipo_acao', 'nome_coordenador', 'local_evento', 'descricao', 'data_evento', 'horario_inicial', 'horario_final', 'campus'];
        
        foreach ($campos_obrigatorios as $campo) {
            if (empty($_POST[$campo])) {
                throw new Exception("O campo " . str_replace('_', ' ', $campo) . " é obrigatório.");
            }
        }
        
        $foto_capa = '';
        if (isset($_FILES['foto_capa']) && $_FILES['foto_capa']['error'] == 0) {
            $foto_capa = uploadArquivo($_FILES['foto_capa'], 'uploads/capas');
            if (!$foto_capa) {
                throw new Exception("Erro no upload da foto de capa. Verifique se é uma imagem válida.");
            }
        }
        
        $galeria_fotos = [];
        if (isset($_FILES['galeria_fotos'])) {
            for ($i = 0; $i < count($_FILES['galeria_fotos']['name']); $i++) {
                if ($_FILES['galeria_fotos']['error'][$i] == 0) {
                    $arquivo_galeria = [
                        'name' => $_FILES['galeria_fotos']['name'][$i],
                        'tmp_name' => $_FILES['galeria_fotos']['tmp_name'][$i],
                        'error' => $_FILES['galeria_fotos']['error'][$i]
                    ];
                    $nome_arquivo = uploadArquivo($arquivo_galeria, 'uploads/galerias');
                    if ($nome_arquivo) {
                        $galeria_fotos[] = $nome_arquivo;
                    }
                }
            }
        }
        
        $sql = "INSERT INTO acoes (tipo_acao, nome_coordenador, local_evento, descricao, data_evento, horario_inicial, horario_final, campus, foto_capa, galeria_fotos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['tipo_acao'],
            $_POST['nome_coordenador'],
            $_POST['local_evento'],
            $_POST['descricao'],
            $_POST['data_evento'],
            $_POST['horario_inicial'],
            $_POST['horario_final'],
            $_POST['campus'],
            $foto_capa,
            implode(',', $galeria_fotos)
        ]);
        
        $mensagem = "Ação cadastrada com sucesso!";
        $tipo_mensagem = "success";
        
    } catch (Exception $e) {
        $mensagem = "Erro: " . $e->getMessage();
        $tipo_mensagem = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistema de Cadastro de Ações</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
/* --- SEU CSS EXISTENTE --- */
body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container-main{background: rgba(255,255,255,0.95); border-radius:20px; box-shadow:0 20px 40px rgba(0,0,0,0.1); backdrop-filter: blur(10px); margin:2rem auto; padding:2rem;}
.header-title{color:#4a5568;font-weight:700;text-align:center;margin-bottom:2rem;position:relative;}
.header-title::after{content:'';position:absolute;bottom:-10px;left:50%;transform:translateX(-50%);width:100px;height:4px;background:linear-gradient(90deg,#667eea,#764ba2);border-radius:2px;}
.form-control,.form-select{border:2px solid #e2e8f0;border-radius:12px;padding:12px 16px;transition:all 0.3s ease;background:rgba(255,255,255,0.9);}
.form-control:focus,.form-select:focus{border-color:#667eea;box-shadow:0 0 0 0.2rem rgba(102,126,234,0.25);transform:translateY(-2px);}
.btn-primary{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border:none;border-radius:12px;padding:12px 30px;font-weight:600;transition:all 0.3s ease;}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 10px 20px rgba(102,126,234,0.3);}
.btn-secondary{background:linear-gradient(135deg,#718096 0%,#4a5568 100%);border:none;border-radius:12px;padding:12px 30px;font-weight:600;transition:all 0.3s ease;}
.btn-secondary:hover{transform:translateY(-2px);box-shadow:0 10px 20px rgba(113,128,150,0.3);}
.form-label{font-weight:600;color:#4a5568;margin-bottom:8px;}
.card{border:none;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,0.05);transition:all 0.3s ease;}
.card:hover{transform:translateY(-2px);box-shadow:0 8px 15px rgba(0,0,0,0.1);}
.alert{border-radius:12px;border:none;font-weight:500;}
.file-upload-area{border:2px dashed #cbd5e0;border-radius:12px;padding:2rem;text-align:center;transition:all 0.3s ease;background:rgba(247,250,252,0.5);}
.file-upload-area:hover{border-color:#667eea;background:rgba(102,126,234,0.05);}
.icon-wrapper{display:inline-flex;align-items:center;justify-content:center;width:50px;height:50px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:50%;color:white;margin-bottom:1rem;}
/* --- OVERLAY SENHA --- */
#password-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.85);display:flex;align-items:center;justify-content:center;z-index:9999;}
#password-box{background:#fff;padding:2rem;border-radius:15px;width:300px;text-align:center;}
#password-box input{width:100%;padding:8px;margin-top:10px;}
#password-box button{margin-top:10px;padding:8px 16px;cursor:pointer;}
</style>
</head>
<body>

<?php if (!$unlocked): ?>
<div id="password-overlay">
    <div id="password-box">
        <h2>Informe a senha</h2>
        <?php if ($message_senha): ?>
            <p style="color:red;"><?php echo htmlspecialchars($message_senha); ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="password" name="senha_site" placeholder="Senha" required autofocus>
            <button type="submit">Liberar</button>
        </form>
        <p style="font-size:12px; color:#666; margin-top:5px;">Tentativas: <?php echo (int)$_SESSION['tries']; ?> / <?php echo $MAX_TRIES; ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($unlocked): ?>

<div class="container">
    <div class="container-main">
        <h1 class="header-title">
            <i class="fas fa-calendar-plus me-3"></i>
            Sistema de Cadastro de Ações
        </h1>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $tipo_mensagem == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo $mensagem; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="text-center mt-4">
                <a href="?logout=1" class="btn btn-secondary btn-lg">
                    <i class="fas fa-lock me-2"></i>Bloquear Site
                </a>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
