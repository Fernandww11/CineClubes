<?php
require_once 'config.php';

// Variáveis de feedback
$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Conexão com o banco
        $pdo = conectarBanco();
        

        // 1. Validação de campos obrigatórios
        $campos_obrigatorios = [
            'tipo_acao', 'nome_coordenador', 'local_evento',
            'descricao', 'data_evento', 'horario_inicial',
            'horario_final', 'campus'
        ];
        
        foreach ($campos_obrigatorios as $campo) {
            if (empty($_POST[$campo])) {
                throw new Exception("O campo " . str_replace('_', ' ', $campo) . " é obrigatório.");
            }
        }
        

        // 2. Upload da foto de capa
        $foto_capa = '';
        if (isset($_FILES['foto_capa']) && $_FILES['foto_capa']['error'] == 0) {
            $foto_capa = uploadArquivo($_FILES['foto_capa'], 'uploads/capas');
            if (!$foto_capa) {
                throw new Exception("Erro no upload da foto de capa. Verifique se é uma imagem válida.");
            }
        }
        

        // 3. Upload das fotos da galeria
        $galeria_fotos = [];
        if (isset($_FILES['galeria_fotos'])) {
            for ($i = 0; $i < count($_FILES['galeria_fotos']['name']); $i++) {
                if ($_FILES['galeria_fotos']['error'][$i] == 0) {
                    $arquivo_galeria = [
                        'name'     => $_FILES['galeria_fotos']['name'][$i],
                        'tmp_name' => $_FILES['galeria_fotos']['tmp_name'][$i],
                        'error'    => $_FILES['galeria_fotos']['error'][$i]
                    ];
                    $nome_arquivo = uploadArquivo($arquivo_galeria, 'uploads/galerias');
                    if ($nome_arquivo) {
                        $galeria_fotos[] = $nome_arquivo;
                    }
                }
            }
        }
        

        // 4. Inserir dados no banco
        $sql = "INSERT INTO acoes 
                (tipo_acao, nome_coordenador, local_evento, descricao, 
                 data_evento, horario_inicial, horario_final, campus, 
                 foto_capa, galeria_fotos) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
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
            implode(',', $galeria_fotos) // salva a lista como string separada por vírgula
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
    <!-- Cabeçalho / Metadados e CSS -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ações Cadastradas - Sistema de Ações</title>
    
    <!-- Bootstrap / FontAwesome / Lightbox -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">

    <!-- Estilo Customizado -->
    <link rel="stylesheet" href="styles.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Bungee&family=New+Amsterdam&family=Staatliches&display=swap');
    </style>

    <!-- Script de bloqueio por senha -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const senhaCorreta = 'adm123';
        const btn = document.getElementById('btnVerificarSenha');
        const input = document.getElementById('senhaInput');
        const erro = document.getElementById('erroSenha');
        const bloqueio = document.getElementById('bloqueioSenha');

        // Verifica senha ao clicar
        btn.addEventListener('click', function() {
            if(input.value === senhaCorreta){
                bloqueio.style.display = 'none'; // libera a página
            } else {
                erro.style.display = 'block';
                input.value = '';
                input.focus();
            }
        });

        // Permite pressionar Enter
        input.addEventListener('keyup', function(e){
            if(e.key === 'Enter') btn.click();
        });
    });
    </script>
</head>
<body>


    <div id="bloqueioSenha" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); display:flex; align-items:center; justify-content:center; z-index:9999;">
        <div style="background:white; padding:2rem; border-radius:10px; text-align:center; max-width:400px; width:90%;">
            <h3>Digite a senha</h3>
            <input type="password" id="senhaInput" placeholder="Senha" class="form-control mt-3">
            <button id="btnVerificarSenha" class="btn btn-primary mt-3 w-100">Entrar</button>
            <p id="erroSenha" style="color:red; display:none; margin-top:0.5rem;">Senha incorreta!</p>
        </div>
    </div>
    

     <!-- Barra de Navegação -->
     <nav class="navbar navbar-expand-lg navbar-dark border-secondary">
        <div class="container">
            <div class="navbar-brand d-flex align-items-center">
                <a class="nav-link active" href="home.html">
                    <img src="https://uploads.onecompiler.io/43vms4uzs/43vmrkwnq/favicon_white.png" 
                         alt="Logo Cine IFMG" style="height:48px;">
                </a>
            </div>
            
            <!-- Botão Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Links do Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="home.html">INÍCIO</a></li>
                    <li class="nav-item"><a class="nav-link" href="cineclubes.php">CINECLUBES</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.html">SOBRE</a></li>
                    <li class="nav-item"><a class="nav-link active" href="cadAcao.php">CADASTRAR EVENTO</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.html">ACESSO</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <div class="container">
        <div class="container-main">
            <h1 class="header-title">
                <i class="fas fa-calendar-plus me-3"></i> Sistema de Cadastro de Ações
            </h1>
            
            <!-- Mensagem de sucesso/erro -->
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $tipo_mensagem == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Formulário -->
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Tipo de ação -->
                    <div class="col-md-6 mb-3">
                        <label for="tipo_acao" class="form-label">
                            <i class="fas fa-tag me-2"></i>Tipo de Ação
                        </label>
                        <select class="form-select" id="tipo_acao" name="tipo_acao" required>
                            <option value="">Selecione o tipo de ação</option>
                            <option value="filme">Filme</option>
                            <option value="debate">Debate</option>
                            <option value="curta">Curta</option>
                            <option value="palestra">Palestra</option>
                        </select>
                    </div>

                    <!-- Campus -->
                    <div class="col-md-6 mb-3">
                        <label for="campus" class="form-label">
                            <i class="fas fa-university me-2"></i>Campus
                        </label>
                        <select class="form-select" id="campus" name="campus" required>
                            <option value="">Selecione o campus</option>
                            <option value="RN">Cineves</option>
                            <option value="C">Ouro Preto</option>
                            <option value="IP">Betim</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Nome coordenador -->
                    <div class="col-md-6 mb-3">
                        <label for="nome_coordenador" class="form-label">
                            <i class="fas fa-user me-2"></i>Nome do Coordenador
                        </label>
                        <input type="text" class="form-control" id="nome_coordenador" name="nome_coordenador" required>
                    </div>
                    
                    <!-- Local evento -->
                    <div class="col-md-6 mb-3">
                        <label for="local_evento" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Local do Evento
                        </label>
                        <input type="text" class="form-control" id="local_evento" name="local_evento" required>
                    </div>
                </div>

                <!-- Descrição -->
                <div class="mb-3">
                    <label for="descricao" class="form-label">
                        <i class="fas fa-align-left me-2"></i>Descrição
                    </label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="4" required></textarea>
                </div>

                <div class="row">
                    <!-- Data -->
                    <div class="col-md-4 mb-3">
                        <label for="data_evento" class="form-label">
                            <i class="fas fa-calendar me-2"></i>Data do Evento
                        </label>
                        <input type="date" class="form-control" id="data_evento" name="data_evento" required>
                    </div>

                    <!-- Horário inicial -->
                    <div class="col-md-4 mb-3">
                        <label for="horario_inicial" class="form-label">
                            <i class="fas fa-clock me-2"></i>Horário Inicial
                        </label>
                        <input type="time" class="form-control" id="horario_inicial" name="horario_inicial" required>
                    </div>

                    <!-- Horário final -->
                    <div class="col-md-4 mb-3">
                        <label for="horario_final" class="form-label">
                            <i class="fas fa-clock me-2"></i>Horário Final
                        </label>
                        <input type="time" class="form-control" id="horario_final" name="horario_final" required>
                    </div>
                </div>

                <div class="row">
                    <!-- Foto capa -->
                    <div class="col-md-6 mb-3">
                        <label for="foto_capa" class="form-label">
                            <i class="fas fa-image me-2"></i>Foto de Capa
                        </label>
                        <input type="file" class="form-control" id="foto_capa" name="foto_capa" accept="image/*">
                        <small class="text-muted">Selecione uma imagem para a capa do evento</small>
                    </div>

                    <!-- Galeria -->
                    <div class="col-md-6 mb-3">
                        <label for="galeria_fotos" class="form-label">
                            <i class="fas fa-images me-2"></i>Galeria de Fotos
                        </label>
                        <input type="file" class="form-control" id="galeria_fotos" name="galeria_fotos[]" accept="image/*" multiple>
                        <small class="text-muted">Selecione múltiplas imagens para a galeria</small>
                    </div>
                </div>

                <!-- Botões -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-save me-2"></i>Cadastrar Ação
                    </button>
                    <a href="cineclubes.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-eye me-2"></i>Ver Ações Cadastradas
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


