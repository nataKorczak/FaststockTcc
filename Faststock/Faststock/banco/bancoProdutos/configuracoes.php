<?php

$dbHost = 'Localhost';
$dbUsername = 'root';
$dbPassword = 'play123';
$dbName = 'formulario_produto';

$conexao = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);

/*if($conexao->connect_errno)
{
    echo "Erro";
} else{
    echo " Conexão foi um sucesso";
}*/ 
?>