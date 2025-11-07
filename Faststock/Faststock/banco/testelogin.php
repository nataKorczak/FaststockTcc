<?php
session_start();

// Verificar se o formulário foi submetido
if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    
    include_once('config.php');
    $email = $_POST['email'];
    $senha_digitada = $_POST['senha'];

    // Debug: Verificar dados recebidos
    error_log("Tentativa de login: Email: $email");

    // Buscar usuário no banco
    $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $conexao->prepare($sql);
    
    if(!$stmt) {
        error_log("Erro no prepare: " . $conexao->error);
        header('Location: ../Paginas/login.php?erro=erro_bd');
        exit;
    }
    
    $stmt->bind_param("s", $email);
    
    if(!$stmt->execute()) {
        error_log("Erro no execute: " . $stmt->error);
        header('Location: ../Paginas/login.php?erro=erro_bd');
        exit;
    }
    
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        
 
        error_log("Hash no BD: " . $usuario['senha']);
        error_log("Senha digitada: " . $senha_digitada);
        

        if(password_verify($senha_digitada, $usuario['senha'])) {

            $_SESSION['email'] = $usuario['email'];
            $_SESSION['user_id'] = $usuario['id']; 
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['username'] = $usuario['username'];
            
            error_log("Login bem-sucedido para: " . $email);
            
            header('Location: ../Paginas/home.php');
            exit;
        } else {

            error_log("Senha incorreta para: " . $email);
            unset($_SESSION['email']);
            unset($_SESSION['user_id']);
            header('Location: ../Paginas/login.php?erro=senha');
            exit;
        }
    } else {

        error_log("Email não encontrado: " . $email);
        unset($_SESSION['email']);
        unset($_SESSION['user_id']);
        header('Location: ../Paginas/login.php?erro=email');
        exit;
    }
} else {

    error_log("Campos vazios no login");
    header('Location: ../Paginas/login.php?erro=campos_vazios');
    exit;
}
?>