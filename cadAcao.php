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
