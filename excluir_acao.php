<?php
require_once 'config.php';

// Verificar se foi enviado um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: cadAcao.php?erro=id_invalido');
    exit;
}

$id_acao = (int)$_GET['id'];

try {
    $pdo = conectarBanco();
    
    // Buscar informações da ação antes de excluir (para deletar arquivos)
    $sql = "SELECT foto_capa, galeria_fotos FROM acoes WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_acao]);
    $acao = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$acao) {
        header('Location: cadAcao.php?erro=acao_nao_encontrada');
        exit;
    }
    
    // Excluir arquivos de imagem do servidor
    if ($acao['foto_capa'] && file_exists('uploads/capas/' . $acao['foto_capa'])) {
        unlink('uploads/capas/' . $acao['foto_capa']);
    }
    
    if ($acao['galeria_fotos']) {
        $fotos_galeria = explode(',', $acao['galeria_fotos']);
        foreach ($fotos_galeria as $foto) {
            $foto = trim($foto);
            if ($foto && file_exists('uploads/galerias/' . $foto)) {
                unlink('uploads/galerias/' . $foto);
            }
        }
    }
    
    // Excluir registros do banco de dados
    // A tabela uploads será excluída automaticamente devido ao CASCADE
    $sql = "DELETE FROM acoes WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_acao]);
    
    header('Location: cadAcao.php?sucesso=acao_excluida');
    exit;
    
} catch (Exception $e) {
    header('Location:  cadAcao.php?erro=erro_exclusao&msg=' . urlencode($e->getMessage()));
    exit;
}
?>

