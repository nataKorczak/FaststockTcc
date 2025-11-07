<?php
session_start();
include_once('config.php');

if(isset($_POST['submit'])) {
    $id = $_POST['id'];
    $email = $_POST['emailregister'] ?? '';
    
    if(empty($email)) {
        header('Location: ../Paginas/editemail.php?id='.$id.'&erro=email_vazio');
        exit;
    }
    
    // Verifica se email já existe (excluindo o usuário atual)
    $stmt_check = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt_check->bind_param("si", $email, $id);
    $stmt_check->execute();
    
    if($stmt_check->get_result()->num_rows > 0) {
        header('Location: ../Paginas/editemail.php?id='.$id.'&erro=email_em_uso');
        exit;
    }
    
    $sql = "UPDATE usuarios SET email = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $email, $id);
    
    if($stmt->execute()) {
        // Atualiza o email na sessão
        $_SESSION['email'] = $email;
        header('Location: ../Paginas/usuario.php?sucesso=email_alterado');
        exit;
    } else {
        header('Location: ../Paginas/editemail.php?id='.$id.'&erro=erro_banco');
        exit;
    }
} else {
    header('Location: ../Paginas/usuario.php');
    exit;
}
?>