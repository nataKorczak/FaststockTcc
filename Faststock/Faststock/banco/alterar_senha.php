<?php
session_start();
include_once('config.php');

if(isset($_POST['submit'])) {
    $id = $_POST['id'];
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirm_senha = $_POST['confirm_senha'] ?? '';

    
    $sql = "SELECT senha FROM usuarios WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if(!$usuario) {
        header('Location: ../Paginas/editsenha.php?id='.$id.'&erro=usuario_nao_encontrado');
        exit;
    }

    
    if(!password_verify($senha_atual, $usuario['senha'])) {
        header('Location: ../Paginas/editsenha.php?id='.$id.'&erro=senha_incorreta');
        exit;
    }

    
    if($nova_senha !== $confirm_senha) {
        header('Location: ../Paginas/editsenha.php?id='.$id.'&erro=senhas_nao_coincidem');
        exit;
    }

    
    if(strlen($nova_senha) < 6) {
        header('Location: ../Paginas/editsenha.php?id='.$id.'&erro=senha_curta');
        exit;
    }

    
    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $sqlUpdate = "UPDATE usuarios SET senha = ? WHERE id = ?";
    $stmtUpdate = $conexao->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $hash, $id);

    if($stmtUpdate->execute()) {
        header('Location: ../Paginas/editsenha.php?id='.$id.'&sucesso=senha_alterada');
        exit;
    } else {
        header('Location: ../Paginas/editsenha.php?id='.$id.'&erro=erro_banco');
        exit;
    }
} else {
    header('Location: ../Paginas/usuario.php');
    exit;
}
?>