<?php

// Importar Configuração do Banco
require_once 'config.php';


// Tratamento de Mensagens de Feedback (Sucesso / Erro)
$mensagem = '';
$tipo_mensagem = '';

if (isset($_GET['sucesso'])) {
    switch ($_GET['sucesso']) {
        case 'acao_excluida':
            $mensagem = 'Ação excluída com sucesso!';
            $tipo_mensagem = 'success';
            break;
    }
}

if (isset($_GET['erro'])) {
    switch ($_GET['erro']) {
        case 'id_invalido':
            $mensagem = 'ID da ação inválido.';
            $tipo_mensagem = 'danger';
            break;
        case 'acao_nao_encontrada':
            $mensagem = 'Ação não encontrada.';
            $tipo_mensagem = 'danger';
            break;
        case 'erro_exclusao':
            $mensagem = 'Erro ao excluir ação: ' . (isset($_GET['msg']) ? $_GET['msg'] : 'Erro desconhecido');
            $tipo_mensagem = 'danger';
            break;
    }
}


// Da erro ao tentar abrir a pagina por não achar o banco acoes
/*
try {
    $pdo = conectarBanco();
    $sql = "SELECT * FROM acoes ORDER BY data_cadastro DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $acoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $erro = "Erro ao carregar ações: " . $e->getMessage();
}
*/


// Funções Auxiliares de Formatação
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

function formatarHorario($horario) {
    return date('H:i', strtotime($horario));
}

function obterIconeTipo($tipo) {
    $icones = [
        'filme' => 'fas fa-film',
        'debate' => 'fas fa-comments',
        'curta' => 'fas fa-video',
        'palestra' => 'fas fa-microphone'
    ];
    return $icones[$tipo] ?? 'fas fa-calendar';
}

function obterCorTipo($tipo) {
    $cores = [
        'filme' => 'primary',
        'debate' => 'success',
        'curta' => 'warning',
        'palestra' => 'info'
    ];
    return $cores[$tipo] ?? 'secondary';
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

</head>
<body>

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
                    <li class="nav-item"><a class="nav-link active" href="cineclubes.php">CINECLUBES</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.html">SOBRE</a></li>
                    <li class="nav-item"><a class="nav-link" href="cadAcao.php">CADASTRAR EVENTO</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.html">ACESSO</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container">
        <div class="container-main">
            <h1 class="header-title">
                <i class="fas fa-list me-3"></i>
                Ações Cadastradas
            </h1>
            
            <!-- Botão de Novo Cadastro -->
            <div class="text-center mb-4">
                <a href="cadAcao.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Cadastrar Nova Ação
                </a>
            </div>
            
            <!-- Mensagens de Feedback -->
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $tipo_mensagem == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Erro ao carregar -->
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <!-- Estado Vazio -->
            <?php if (empty($acoes)): ?>
                <div class="empty-state">
                    <h3>Nenhuma ação cadastrada</h3>
                    <p>Comece cadastrando sua primeira ação clicando no botão acima.</p>
                </div>
            
            <!-- Listagem das Ações -->
            <?php else: ?>
                <div class="row">
                    <?php foreach ($acoes as $acao): ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card action-card">
                                
                                <!-- Cabeçalho do Card -->
                                <div class="card-header-custom position-relative">
                                    <div class="campus-badge">
                                        <i class="fas fa-university me-1"></i>
                                        Campus <?php echo htmlspecialchars($acao['campus']); ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-2">
                                                <i class="<?php echo obterIconeTipo($acao['tipo_acao']); ?> me-2"></i>
                                                <?php echo ucfirst(htmlspecialchars($acao['tipo_acao'])); ?>
                                            </h5>
                                            <small class="opacity-75">
                                                <i class="fas fa-calendar me-1"></i>
                                                Cadastrado em <?php echo formatarData($acao['data_cadastro']); ?>
                                            </small>
                                        </div>
                                        <button class="btn btn-outline-light btn-sm" 
                                                onclick="confirmarExclusao(<?php echo $acao['id']; ?>)"
                                                title="Excluir ação">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Corpo do Card -->
                                <div class="card-body-custom">
                                    <!-- Foto de capa -->
                                    <?php if ($acao['foto_capa']): ?>
                                        <img src="uploads/capas/<?php echo htmlspecialchars($acao['foto_capa']); ?>" 
                                             alt="Capa do evento" class="capa-image">
                                    <?php endif; ?>
                                    
                                    <!-- Coordenador -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-user"></i></div>
                                        <div><strong>Coordenador:</strong><br><?php echo htmlspecialchars($acao['nome_coordenador']); ?></div>
                                    </div>
                                    
                                    <!-- Local -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                        <div><strong>Local:</strong><br><?php echo htmlspecialchars($acao['local_evento']); ?></div>
                                    </div>
                                    
                                    <!-- Data -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-calendar"></i></div>
                                        <div><strong>Data:</strong><br><?php echo formatarData($acao['data_evento']); ?></div>
                                    </div>
                                    
                                    <!-- Horário -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                                        <div><strong>Horário:</strong><br>
                                            <?php echo formatarHorario($acao['horario_inicial']); ?> às 
                                            <?php echo formatarHorario($acao['horario_final']); ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Descrição -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-align-left"></i></div>
                                        <div><strong>Descrição:</strong><br><?php echo nl2br(htmlspecialchars($acao['descricao'])); ?></div>
                                    </div>
                                    
                                    <!-- Galeria de Fotos -->
                                    <?php if ($acao['galeria_fotos']): ?>
                                        <div class="mt-3">
                                            <button id="btnGaleria-<?php echo $acao['id']; ?>">FOTOS DO EVENTO</button>
                                            <div id="carrossel-<?php echo $acao['id']; ?>" class="carrossel-container" style="display:none;">
                                                <?php 
                                                $fotos = explode(',', $acao['galeria_fotos']);
                                                foreach ($fotos as $index => $foto): 
                                                    if (trim($foto)):
                                                ?>
                                                    <div class="carrossel-slide" style="<?php echo $index === 0 ? '' : 'display:none;'; ?>">
                                                        <img src="uploads/galerias/<?php echo htmlspecialchars(trim($foto)); ?>" 
                                                            alt="Foto da galeria" class="carrossel-img">
                                                    </div>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                ?>
                                                <button class="prev-btn">⬅</button>
                                                <button class="next-btn">➡</button>
                                                <button class="close-btn">✖</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div> <!-- fim card-body -->
                            </div> <!-- fim card -->
                        </div> <!-- fim coluna -->
                    <?php endforeach; ?>
                </div> <!-- fim row -->
            <?php endif; ?>
            
            <!-- Botão Voltar (parece inutil ja que existe o botao de cadastrar nova ação para voltar pra tela)
            <div class="text-center mt-4">
                <a href="cadAcao.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Cadastro
                </a>
            </div>
        </div>
    </div>
    -->

    <!-- Scripts JS -->
    <script src="js/carrossel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
    <script src="js/galeria.js"></script>
    
    <!-- Fechar alertas automaticamente após 5s -->
    <script>
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>

