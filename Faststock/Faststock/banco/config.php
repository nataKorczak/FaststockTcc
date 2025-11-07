<?php
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = 'play123';
$dbName = 'formulario';

$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if($conexao->connect_errno) {

    error_log("Erro de conexão MySQL: " . $conexao->connect_error);
    die("Erro de conexão com o banco de dados.");
}


$conexao->set_charset("utf8mb4");
?>