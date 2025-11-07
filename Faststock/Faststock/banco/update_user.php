<?php
session_start();
include_once('../banco/config.php');

if(isset($_POST['submit'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['emailregister'];
    $username = $_POST['apelido'];
    $telefone = $_POST['telefone'];
    $senha = $_POST['senharegister'];
    $confirmasenha = $_POST['confirmsenha'];

    if(!empty($senha)) {
        if($senha !== $confirmasenha) {
            die("Erro: Senhas não coincidem!");
        }
        $senha_cripto = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome=?, email=?, username=?, telefone=?, senha=?, confirmarsenha=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssssi", $nome, $email, $username, $telefone, $senha_cripto, $senha_cripto, $id);
    } else {

        $sql = "UPDATE usuarios SET nome=?, email=?, username=?, telefone=? WHERE id=?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $email, $username, $telefone, $id);
    }

    if($stmt->execute()) {
        header('Location: ../Paginas/usuario.php');
        exit;
    } else {
        echo "Erro ao atualizar: " . $conexao->error;
    }
}
?>