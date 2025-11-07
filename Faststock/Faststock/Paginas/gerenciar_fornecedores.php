<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$logado = $_SESSION['email'];
$user_id = $_SESSION['user_id'];


include_once('../banco/bancoFornecedores/configForn.php');


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao'])) {
    if ($_GET['acao'] === 'carregar') {
        $sql = "SELECT * FROM fornecedores ORDER BY razaoSocial";
        $result = mysqli_query($conexao, $sql);
        
        if ($result) {
            $fornecedores = [];
            while($row = mysqli_fetch_assoc($result)) {
                $fornecedores[] = $row;
            }
            echo json_encode(['status' => 'sucesso', 'dados' => $fornecedores]);
        } else {
            echo json_encode(['status' => 'erro', 'msg' => 'Erro ao carregar fornecedores: ' . mysqli_error($conexao)]);
        }
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'adicionar';
    
   
    if ($acao === 'remover') {
        $cnpj = $_POST['cnpj'] ?? '';
        
        if (empty($cnpj)) {
            echo json_encode(['status' => 'erro', 'msg' => 'CNPJ não fornecido']);
            exit;
        }
        
        $query = "DELETE FROM fornecedores WHERE cnpj = '$cnpj'";
        
        if (mysqli_query($conexao, $query)) {
            echo json_encode(['status' => 'sucesso', 'msg' => 'Fornecedor removido com sucesso!']);
        } else {
            echo json_encode(['status' => 'erro', 'msg' => 'Erro ao remover fornecedor: ' . mysqli_error($conexao)]);
        }
        exit;
    }
    
   
    $cnpj = $_POST['cnpj'] ?? '';
    $razao = $_POST['razao'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $contato = $_POST['contato'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $cnpjOriginal = $_POST['cnpjOriginal'] ?? '';

    // Validações básicas
    if (empty($cnpj) || empty($razao) || empty($cep) || empty($contato) || empty($descricao)) {
        echo json_encode(['status' => 'erro', 'msg' => 'Todos os campos obrigatórios devem ser preenchidos!']);
        exit;
    }

    if ($acao === 'adicionar' || ($acao === 'editar' && $cnpj !== $cnpjOriginal)) {
        $check = mysqli_query($conexao, "SELECT * FROM fornecedores WHERE cnpj = '$cnpj'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['status' => 'erro', 'msg' => 'CNPJ já existe!']);
            exit;
        }
    }

    try {
        if ($acao === 'adicionar') {
            $query = "INSERT INTO fornecedores (cnpj, razaoSocial, cep, endereco, contato, descricao) 
                      VALUES ('$cnpj', '$razao', '$cep', '$endereco', '$contato', '$descricao')";
        } else {
            $query = "UPDATE fornecedores 
                      SET cnpj='$cnpj', razaoSocial='$razao', cep='$cep', endereco='$endereco', 
                          contato='$contato', descricao='$descricao' 
                      WHERE cnpj='$cnpjOriginal'";
        }

        if (mysqli_query($conexao, $query)) {
            echo json_encode(['status' => 'sucesso', 'msg' => 'Fornecedor salvo com sucesso!']);
        } else {
            echo json_encode(['status' => 'erro', 'msg' => 'Erro ao salvar fornecedor: ' . mysqli_error($conexao)]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'erro', 'msg' => 'Erro: ' . $e->getMessage()]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Fornecedores - FastStock</title>
    <link rel="stylesheet" href="../Estilo/Gerenciar_fornecedores.css">
    <link rel="stylesheet" href="../Estilo/temaglobal.css">
    <script src="../Configuracao/temaglobal.js"></script>
    
    <style>

        .barra_lateral {
            width: 100px;
            background-color: #3F2605;
            padding-top: 20px;
            transition: width 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 999;
        }

        .barra_lateral:hover {
            width: 220px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
            flex-shrink: 0;
        }

        .logo img {
            width: 100px;
            cursor: pointer;
        }

        .menu {
            flex: 1;
            overflow-y: auto;
        }

        .menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu ul li {
            margin: 10px 0;
        }

        .menu ul li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #fff;
            padding: 20px;
            white-space: nowrap;
            transition: background-color 0.3s;
        }

        .menu ul li a:hover {
            background-color: #603813;
        }

        .menu ul li a img {
            width: 35px;
            margin-right: 10px;
        }

        .texto_menu {
            margin-left: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .barra_lateral:hover .texto_menu {
            opacity: 1;
            pointer-events: auto;
        }

        .conteudo {
            flex: 1;
            padding: 30px;
            margin-left: 100px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
    </style>
</head>
<body>
    <header>
        <h1>FastStock</h1>
    </header>

    <main>

        <nav class="barra_lateral">
            <div class="logo">
                <img src="../Imagens/logo.png" alt="logo" onclick="window.location.href='home.php'">
            </div>
            <div class="menu">
                <ul>
                    <li><a href="home.php"><img src="../Imagens/home.png" alt="Home"><span class="texto_menu">Home</span></a></li>
                    <li><a href="estoque.html"><img src="../Imagens/estoque.png" alt="Estoque"><span class="texto_menu">Estoque</span></a></li>
                    <li><a href="historico.php"><img src="../Imagens/vendas.png" alt="Histórico"><span class="texto_menu">Histórico</span></a></li>
                    <li><a href="usuario.php"><img src="../Imagens/admin.png" alt="Usuário"><span class="texto_menu">Usuário</span></a></li>
                    <li><a href="configuracoes.html"><img src="../Imagens/configuracoes.png" alt="Configurações"><span class="texto_menu">Configurações</span></a></li>
                </ul>
            </div>
        </nav>

        <section class="conteudo">
            <div class="texto_cabecario">
                <p class="texto_pagina">CONTROLE DE FORNECEDORES</p>
            </div>

            <div class="cabecario_adicionar">
                <p>Adicionar Fornecedor</p>
            </div>

            <section class="formulario_produto">
                <div>
                    <label for="cnpj">CNPJ:*</label>
                    <input type="text" id="cnpj" placeholder="00.000.000/0000-00" required>
                </div>

                <div>
                    <label for="nome">Razão Social:*</label>
                    <input type="text" id="nome" placeholder="Nome completo da empresa" required>
                </div>

                <div>
                    <label for="cep">CEP:*</label>
                    <input type="text" id="cep" placeholder="00000-000" required>
                </div>

                <div>
                    <label for="endereco">Endereço:*</label>
                    <input type="text" id="endereco" placeholder="Endereço completo" required>
                </div>

                <div>
                    <label for="numero">Contato:*</label>
                    <input type="text" id="numero" placeholder="(00) 00000-0000" required>
                </div>

                <div>
                    <label for="descricao">Descrição:*</label>
                    <input type="text" id="descricao" placeholder="Descrição do fornecedor" required>
                </div>

                <div class="adicionar_btn">
                    <button id="BtnAdicionar" type="button">ADICIONAR</button>
                </div>
            </section>

            <div class="cabecario_estoque">
                <p>Fornecedores Cadastrados</p>
            </div>

            <section>
                <table id="tabelaFornecedores">
                    <thead>
                        <tr>
                            <th>CNPJ</th>
                            <th>RAZÃO SOCIAL</th>
                            <th>CEP</th>
                            <th>ENDEREÇO</th>
                            <th>CONTATO</th>
                            <th>DESCRIÇÃO</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabela">
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">
                                Carregando fornecedores...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </section>
    </main>

    <button id="btnAcessibilidade">♿ Acessibilidade</button>
    <div id="menuAcessibilidade" class="menu-acessibilidade">
        <button onclick="toggleDaltonismo()">Modo Daltonismo</button>
        <button onclick="toggleFonte()">Aumentar Fonte</button>
        <button onclick="toggleContraste()">Alto Contraste</button>
    </div>

    <script src="../Configuracao/Acessibilidade.js"></script>
    <script src="../Configuracao/Gerenciar.Fornecedores.js"></script>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página Gerenciar Fornecedores carregada');
        });
    </script>
</body>
</html>