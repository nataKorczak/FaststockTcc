<?php
session_start();


if(!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$logado = $_SESSION['email'];
$user_id = $_SESSION['user_id'];
$nome_usuario = $_SESSION['nome'] ?? 'Usuário';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - FastStock</title>
    <link rel="stylesheet" href="../Estilo/Home.css">
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
            overflow-y: auto;
        }

        .barra_lateral:hover {
            width: 220px;
        }

        .barra_lateral:hover ~ .conteudo {
            margin-left: 220px;
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

        .sair {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .sair .btnsair {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff0000;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .sair .btnsair:hover {
            background-color: #ad0000;
        }

        /* CORREÇÃO DO LAYOUT PRINCIPAL */
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
                <img src="../Imagens/logo.png" alt="logo">
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
                <p>Bem-vindo ao sistema FastStock!</p>
            </div>

            <div class="boxs_conteudo">
                <div class="estoque_box">
                    <img src="../Imagens/estoque.png" alt="Estoque">
                    <p>Responsável pelo controle ágil e eficiente do estoque, permitindo adicionar, excluir e editar itens</p>
                </div>

                <div class="vendas_box">
                    <img src="../Imagens/vendas.png" alt="Vendas">
                    <p>Responsável pelo histórico de entrada e saída de itens dentro do estoque</p>
                </div>

                <div class="admin_box">
                    <img src="../Imagens/admin.png" alt="Admin">
                    <p>Responsável pela gestão de permissões e controle de acesso dos utilizadores do sistema</p>
                </div>

                <div class="configuracoes_box">
                    <img src="../Imagens/configuracoes.png" alt="Configurações">
                    <p>Responsável pela personalização do sistema, incluindo ajustes gerais, notificações e preferências do utilizador</p>
                </div>
            </div>

            <div class="sair">
                <a href="../banco/sair.php" class="btnsair">Sair</a>
            </div>
        </section>
    </main>

    <button id="btnAcessibilidade">♿ Acessibilidade</button>
    <div id="menuAcessibilidade" class="menu-acessibilidade">
        <button onclick="toggleDaltonismo()">Modo Daltonismo</button>
        <button onclick="toggleFonte()">Aumentar Fonte</button>
        <button onclick="toggleContraste()">Alto Contraste</button>
    </div>

    <script src="../Configuracao/Acessibilidade.js"></script>

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
</body>
</html>