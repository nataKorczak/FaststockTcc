<?php
session_start();
include_once('config.php');

if(isset($_POST['submit'])) {
    $id = $_POST['id'];
    $username = $_POST['apelido'] ?? '';
    
    if(empty($username)) {
        header('Location: ../Paginas/editusername.php?id='.$id.'&erro=username_vazio');
        exit;
    }
    
    $sql = "UPDATE usuarios SET username = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $username, $id);
    
    if($stmt->execute()) {
        header('Location: ../Paginas/usuario.php?sucesso=username_alterado');
        exit;
    } else {
        header('Location: ../Paginas/editusername.php?id='.$id.'&erro=erro_banco');
        exit;
    }
} else {
    header('Location: ../Paginas/usuario.php');
    exit;
}
?>