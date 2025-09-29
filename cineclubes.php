<?php
require_once 'config.php';

// Verificar mensagens de feedback
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ações Cadastradas - Sistema de Ações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-main {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .header-title {
            color: #4a5568;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .header-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        .action-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
            margin-bottom: 2rem;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border: none;
        }
        
        .card-body-custom {
            padding: 2rem;
        }
        
        .badge-tipo {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .info-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(5px);
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .gallery-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        
        .gallery-thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .gallery-thumb:hover {
            transform: scale(1.1);
            border-color: #667eea;
        }
        
        .capa-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #718096;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .campus-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            color: #4a5568;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }


        .carrossel-container {
            position: relative;
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
        }

        .carrossel-img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .prev-btn, .next-btn, .close-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
            border-radius: 4px;
        }

        .prev-btn { left: 10px; }
        .next-btn { right: 10px; }
        .close-btn { top: 10px; right: 10px; transform: none; }

    </style>
</head>
<body>
    <div class="container">
        <div class="container-main">
            <h1 class="header-title">
                <i class="fas fa-list me-3"></i>
                Ações Cadastradas
            </h1>
            
            <div class="text-center mb-4">
                <a href="cadAcao.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Cadastrar Nova Ação
                </a>
            </div>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $tipo_mensagem == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($acoes)): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Nenhuma ação cadastrada</h3>
                    <p>Comece cadastrando sua primeira ação clicando no botão acima.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($acoes as $acao): ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card action-card">
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
                                
                                <div class="card-body-custom">
                                    <?php if ($acao['foto_capa']): ?>
                                        <img src="uploads/capas/<?php echo htmlspecialchars($acao['foto_capa']); ?>" 
                                             alt="Capa do evento" class="capa-image">
                                    <?php endif; ?>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong>Coordenador:</strong><br>
                                            <?php echo htmlspecialchars($acao['nome_coordenador']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <strong>Local:</strong><br>
                                            <?php echo htmlspecialchars($acao['local_evento']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div>
                                            <strong>Data:</strong><br>
                                            <?php echo formatarData($acao['data_evento']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <strong>Horário:</strong><br>
                                            <?php echo formatarHorario($acao['horario_inicial']); ?> às 
                                            <?php echo formatarHorario($acao['horario_final']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-align-left"></i>
                                        </div>
                                        <div>
                                            <strong>Descrição:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($acao['descricao'])); ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($acao['galeria_fotos']): ?>
                                        <div class="mt-3">
                                            <button id="btnGaleria-<?php echo $acao['id']; ?>">FOTOS DO EVENTO</button>

                                            <!-- Carrossel oculto inicialmente -->
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
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="cadAcao.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Cadastro
                </a>
            </div>
        </div>
    </div>
    <script src="js/carrossel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
    <script src="js/galeria.js"></script>
    <script>
        // Fechar alertas automaticamente após 5 segundos
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

