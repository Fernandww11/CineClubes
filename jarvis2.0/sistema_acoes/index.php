<?php
require_once 'config.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = conectarBanco();
        
        // Validar campos obrigatórios
        $campos_obrigatorios = ['tipo_acao', 'nome_coordenador', 'local_evento', 'descricao', 'data_evento', 'horario_inicial', 'horario_final', 'campus'];
        
        foreach ($campos_obrigatorios as $campo) {
            if (empty($_POST[$campo])) {
                throw new Exception("O campo " . str_replace('_', ' ', $campo) . " é obrigatório.");
            }
        }
        
        // Upload da foto de capa
        $foto_capa = '';
        if (isset($_FILES['foto_capa']) && $_FILES['foto_capa']['error'] == 0) {
            $foto_capa = uploadArquivo($_FILES['foto_capa'], 'uploads/capas');
            if (!$foto_capa) {
                throw new Exception("Erro no upload da foto de capa. Verifique se é uma imagem válida.");
            }
        }
        
        // Upload das fotos da galeria
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
        
        // Inserir no banco de dados
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
        
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
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
        
        .btn-secondary {
            background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(113, 128, 150, 0.3);
        }
        
        .form-label {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }
        
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 500;
        }
        
        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: rgba(247, 250, 252, 0.5);
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            color: white;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
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
                <div class="row">
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
                    
                    <div class="col-md-6 mb-3">
                        <label for="campus" class="form-label">
                            <i class="fas fa-university me-2"></i>Campus
                        </label>
                        <select class="form-select" id="campus" name="campus" required>
                            <option value="">Selecione o campus</option>
                            <option value="RN">RN</option>
                            <option value="C">C</option>
                            <option value="IP">IP</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome_coordenador" class="form-label">
                            <i class="fas fa-user me-2"></i>Nome do Coordenador
                        </label>
                        <input type="text" class="form-control" id="nome_coordenador" name="nome_coordenador" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="local_evento" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Local do Evento
                        </label>
                        <input type="text" class="form-control" id="local_evento" name="local_evento" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="descricao" class="form-label">
                        <i class="fas fa-align-left me-2"></i>Descrição
                    </label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="4" required></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="data_evento" class="form-label">
                            <i class="fas fa-calendar me-2"></i>Data do Evento
                        </label>
                        <input type="date" class="form-control" id="data_evento" name="data_evento" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="horario_inicial" class="form-label">
                            <i class="fas fa-clock me-2"></i>Horário Inicial
                        </label>
                        <input type="time" class="form-control" id="horario_inicial" name="horario_inicial" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="horario_final" class="form-label">
                            <i class="fas fa-clock me-2"></i>Horário Final
                        </label>
                        <input type="time" class="form-control" id="horario_final" name="horario_final" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="foto_capa" class="form-label">
                            <i class="fas fa-image me-2"></i>Foto de Capa
                        </label>
                        <div class="file-upload-area">
                            <div class="icon-wrapper">
                                <i class="fas fa-camera"></i>
                            </div>
                            <input type="file" class="form-control" id="foto_capa" name="foto_capa" accept="image/*">
                            <small class="text-muted">Selecione uma imagem para a capa do evento</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="galeria_fotos" class="form-label">
                            <i class="fas fa-images me-2"></i>Galeria de Fotos
                        </label>
                        <div class="file-upload-area">
                            <div class="icon-wrapper">
                                <i class="fas fa-photo-video"></i>
                            </div>
                            <input type="file" class="form-control" id="galeria_fotos" name="galeria_fotos[]" accept="image/*" multiple>
                            <small class="text-muted">Selecione múltiplas imagens para a galeria</small>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-save me-2"></i>Cadastrar Ação
                    </button>
                    <a href="visualizar.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-eye me-2"></i>Ver Ações Cadastradas
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

