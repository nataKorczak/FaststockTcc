<?php
session_start();
include_once('config.php');

if(isset($_POST['submit'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'] ?? '';
    
    if(empty($nome)) {
        header('Location: ../Paginas/editnome.php?id='.$id.'&erro=nome_vazio');
        exit;
    }
    
    $sql = "UPDATE usuarios SET nome = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $nome, $id);
    
    if($stmt->execute()) {
        header('Location: ../Paginas/usuario.php?sucesso=nome_alterado');
        exit;
    } else {
        header('Location: ../Paginas/editnome.php?id='.$id.'&erro=erro_banco');
        exit;
    }
} else {
    header('Location: ../Paginas/usuario.php');
    exit;
}
?>