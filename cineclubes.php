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



try {
    $pdo = conectarBanco();
    $sql = "SELECT * FROM acoes ORDER BY data_cadastro DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $acoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $erro = "Erro ao carregar ações: " . $e->getMessage();
}



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
    @import url('https://fonts.googleapis.com/css2?family=Bungee&family=New+Amsterdam&family=Staatliches&display=swap' );
    
    /* CSS para padronizar as imagens de capa */
    .capa-image {
        width: 100%;
        height: 180px; /* Altura fixa para padronizar */
        object-fit: cover; /* Garante que a imagem preencha o espaço sem distorcer */
        border-radius: 8px;
        margin-bottom: 10px;
    }
    
    /* Estilos do Carrossel */
    .carrossel-container {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        margin-top: 10px;
    }
    .carrossel-slide {
        display: none;
        text-align: center;
    }
    .carrossel-slide.active {
        display: block;
    }
    .carrossel-img {
        width: 100%;
        height: 250px; /* Altura fixa para as imagens da galeria */
        object-fit: cover;
    }
    .carrossel-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        z-index: 10;
    }
    .carrossel-nav-btn.prev { left: 0; border-radius: 0 5px 5px 0; }
    .carrossel-nav-btn.next { right: 0; border-radius: 5px 0 0 5px; }
    .slide-indicator {
        text-align: center;
        margin-top: 5px;
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Estilo Verde Neon para as informações das ações */
    .info-item strong {
        color: #39FF14; /* Verde Neon */
        text-shadow: 0 0 5px #39FF14; /* Efeito neon sutil */
    }
    .info-item div {
        color: #90EE90; /* Um verde mais claro para o valor */
    }
    </style>

    <!-- Script de Confirmação de Exclusão e Lógica do Carrossel -->
    <script>
    function confirmarExclusao(id) {
        if (confirm("Tem certeza que deseja excluir esta ação? Esta ação é irreversível.")) {
            window.location.href = 'excluir_acao.php?id=' + id;
        }
    }
    
    // Lógica do Carrossel
    function showSlide(containerId, n) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const slides = container.querySelectorAll('.carrossel-slide');
        if (slides.length === 0) return;

        let currentSlideIndex = Array.from(slides).findIndex(slide => slide.classList.contains('active'));
        
        // Se não encontrar o ativo, usa o primeiro
        if (currentSlideIndex === -1) {
             currentSlideIndex = 0;
             slides[0].classList.add('active');
        }

        // Calcula o novo índice
        let newIndex = currentSlideIndex + n;

        // Lógica de loop
        if (newIndex >= slides.length) { newIndex = 0; }
        if (newIndex < 0) { newIndex = slides.length - 1; }

        // Esconde todos e mostra o novo
        slides.forEach(slide => slide.classList.remove('active'));
        slides[newIndex].classList.add('active');

        // Atualiza o indicador
        const indicator = document.getElementById('indicator-' + containerId.split('-')[1]);
        if (indicator) {
            indicator.textContent = (newIndex + 1) + ' de ' + slides.length;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa todos os carrosséis
        document.querySelectorAll('.carrossel-container').forEach(container => {
            const id = container.id.split('-')[1];
            const slides = container.querySelectorAll('.carrossel-slide');
            if (slides.length > 0) {
                slides[0].classList.add('active');
                
                // Adiciona os event listeners para os botões de navegação
                const prevBtn = document.getElementById('prev-' + id);
                const nextBtn = document.getElementById('next-' + id);
                
                if(prevBtn) {
                    prevBtn.addEventListener('click', () => showSlide(container.id, -1));
                }
                if(nextBtn) {
                    nextBtn.addEventListener('click', () => showSlide(container.id, 1));
                }
                
                // Inicializa o indicador
                const indicator = document.getElementById('indicator-' + id);
                if (indicator) {
                    indicator.textContent = '1 de ' + slides.length;
                }
            }
        });
        
        // Lógica para mostrar/esconder o carrossel ao clicar no botão
        document.querySelectorAll('[id^="btnGaleria-"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.id.split('-')[1];
                const carrossel = document.getElementById('carrossel-' + id);
                const indicator = document.getElementById('indicator-' + id);
                
                if (carrossel) {
                    const isHidden = carrossel.style.display === 'none' || carrossel.style.display === '';
                    carrossel.style.display = isHidden ? 'block' : 'none';
                    if (indicator) indicator.style.display = isHidden ? 'block' : 'none';
                    this.textContent = isHidden ? 'ESCONDER FOTOS' : 'FOTOS DO EVENTO';
                    
                    // Garante que o primeiro slide esteja ativo ao abrir
                    if (isHidden) {
                        showSlide(carrossel.id, 0); 
                    }
                }
            });
        });
    });
    </script>

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
                    <li class="nav-item"><a class="nav-link" href="inicio.php">INÍCIO</a></li>
                    <li class="nav-item"><a class="nav-link" href="cineclubes.php">CINECLUBES</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.html">SOBRE</a></li>
                    <li class="nav-item"><a class="nav-link active" href="cadAcao.php">CADASTRAR EVENTO</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">ACESSO</a></li>
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
            <?php if ($mensagem ): ?>
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
                                        <div><strong>Coordenador:</strong>  
<?php echo htmlspecialchars($acao['nome_coordenador']); ?></div>
                                    </div>
                                    
                                    <!-- Local -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                        <div><strong>Local:</strong>  
<?php echo htmlspecialchars($acao['local_evento']); ?></div>
                                    </div>
                                    
                                    <!-- Data -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-calendar"></i></div>
                                        <div><strong>Data:</strong>  
<?php echo formatarData($acao['data_evento']); ?></div>
                                    </div>
                                    
                                    <!-- Horário -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-clock"></i></div>
                                        <div><strong>Horário:</strong>  

                                            <?php echo formatarHorario($acao['horario_inicial']); ?> às 
                                            <?php echo formatarHorario($acao['horario_final']); ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Descrição -->
                                    <div class="info-item">
                                        <div class="info-icon"><i class="fas fa-align-left"></i></div>
                                        <div><strong>Descrição:</strong>  
<?php echo nl2br(htmlspecialchars($acao['descricao'])); ?></div>
                                    </div>
                                    
                                    <!-- Galeria de Fotos -->
                                    <?php if ($acao['galeria_fotos']): 
                                        $fotos = explode(',', $acao['galeria_fotos']);
                                        $fotos = array_filter($fotos, 'trim'); // Remove entradas vazias
                                        if (count($fotos) > 0):
                                    ?>
                                        <div class="mt-3">
                                            <button id="btnGaleria-<?php echo $acao['id']; ?>" class="btn btn-sm btn-info w-100 mb-2">FOTOS DO EVENTO</button>
                                            <div id="carrossel-<?php echo $acao['id']; ?>" class="carrossel-container" style="display:none;">
                                                
                                                <?php foreach ($fotos as $index => $foto): ?>
                                                    <div class="carrossel-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                                        <img src="uploads/galerias/<?php echo htmlspecialchars(trim($foto)); ?>" 
                                                            alt="Foto da galeria <?php echo $index + 1; ?>" class="carrossel-img">
                                                    </div>
                                                <?php endforeach; ?>
                                                
                                                <!-- Botões de Navegação -->
                                                <?php if (count($fotos) > 1): ?>
                                                    <button id="prev-<?php echo $acao['id']; ?>" class="carrossel-nav-btn prev">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </button>
                                                    <button id="next-<?php echo $acao['id']; ?>" class="carrossel-nav-btn next">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                            </div>
                                            <div id="indicator-<?php echo $acao['id']; ?>" class="slide-indicator" style="display:none;"></div>
                                        </div>
                                    <?php endif; endif; ?>
                                    
                                </div>
                                
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
</body>
</html>
