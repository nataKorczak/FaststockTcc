<?php
session_start();
include_once('../banco/config.php');

if((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true))
{
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: ../Paginas/login.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;

if($user_id > 0) {
    $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $id = $user_data['id'];
        $nome = $user_data['nome'];
        $username = $user_data['username'];
        $email = $user_data['email'];
        $telefone = $user_data['telefone'] ?? 'Não informado';
        $dataCriacao = $user_data['data_criacao'] ?? date('Y-m-d H:i:s');
    } else {

        session_destroy();
        header('Location: ../Paginas/login.php');
        exit;
    }
} else {

    session_destroy();
    header('Location: ../Paginas/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário - FastStock</title>
    <link rel="stylesheet" href="../Estilo/Usuario.css">

    <link rel="stylesheet" href="../Estilo/temaglobal.css">
    <script src="../Configuracao/temaglobal.js"></script>
    <style>

        
        .perfil-simples {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .cabecalho-simples {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .cabecalho-simples h1 {
            color: #3F2605;
            font-size: 2rem;
            margin-bottom: 5px;
        }
        
        .cabecalho-simples p {
            color: #666;
            font-size: 1rem;
        }
        
        .card-simples {
            background: #2F4F4F;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #3a5a5a;
        }
        
        .info-linha {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .info-linha:last-child {
            border-bottom: none;
        }
        
        .info-grupo {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .info-titulo {
            color: #F1E4D1;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-valor {
            color: #fff;
            font-size: 1rem;
        }
        
        .btn-simples {
            background: #3F2605;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        
        .btn-simples:hover {
            background: #603813;
            border-color: #F1E4D1;
        }
        
        .btn-destaque {
            background: #603813;
            padding: 10px 20px;
            font-weight: 600;
        }
        
        .btn-destaque:hover {
            background: #7a4520;
        }
        
        .data-info {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #F1E4D1;
            font-size: 0.9rem;
        }
        
        /* Estados dos itens */
        .info-linha:hover {
            background: rgba(63, 38, 5, 0.1);
            margin: 0 -10px;
            padding: 15px 10px;
            border-radius: 6px;
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .perfil-simples {
                padding: 10px;
            }
            
            .card-simples {
                padding: 20px;
            }
            
            .info-linha {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .btn-simples {
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="mudar_menu" hidden>

    <header>
        <h1>FastStock</h1>
    </header>

    <main>
        <div id="sidebar"></div>

        <script>
            fetch("../Componentes/sidebar.html")
                .then(res => res.text())
                .then(html => {
                    document.getElementById("sidebar").innerHTML = html;
                });
        </script>

        <section class="conteudo">
            <div class="texto_cabecario">
                <p class="texto_pagina">MEU PERFIL</p>
            </div>

            <div class="perfil-simples">
                <div class="cabecalho-simples">
                    <p>Gerencie suas informações pessoais</p>
                </div>

                <div class="card-simples">
                    <div class="info-linha">
                        <div class="info-grupo">
                            <div class="info-titulo">Nome Completo</div>
                            <div class="info-valor"><?= htmlspecialchars($nome) ?></div>
                        </div>
                        <a class="btn-simples" href="../Paginas/editnome.php?id=<?= $id ?>">
                            Editar
                        </a>
                    </div>

                    <div class="info-linha">
                        <div class="info-grupo">
                            <div class="info-titulo">Username</div>
                            <div class="info-valor">@<?= htmlspecialchars($username) ?></div>
                        </div>
                        <a class="btn-simples" href="../Paginas/editusername.php?id=<?= $id ?>">
                            Editar
                        </a>
                    </div>

                    <div class="info-linha">
                        <div class="info-grupo">
                            <div class="info-titulo">E-mail</div>
                            <div class="info-valor"><?= htmlspecialchars($email) ?></div>
                        </div>
                        <a class="btn-simples" href="../Paginas/editemail.php?id=<?= $id ?>">
                            Editar
                        </a>
                    </div>

                    <div class="info-linha">
                        <div class="info-grupo">
                            <div class="info-titulo">Telefone</div>
                            <div class="info-valor"><?= htmlspecialchars($telefone) ?></div>
                        </div>
                        <a class="btn-simples" href="../Paginas/edittelefone.php?id=<?= $id ?>">
                            Editar
                        </a>
                    </div>

                    <div class="info-linha">
                        <div class="info-grupo">
                            <div class="info-titulo">Senha</div>
                            <div class="info-valor">••••••••</div>
                        </div>
                        <a class="btn-simples btn-destaque" href="../Paginas/editsenha.php?id=<?= $id ?>">
                            Alterar Senha
                        </a>
                    </div>

                    <div class="data-info">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/>
            <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4zM11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm-5 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
        </svg>
        Conta criada em <?= date('d/m/Y', strtotime($dataCriacao)) ?>
    </div>
                </div>
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