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
$logado = $_SESSION['email'] ?? '';


$target_id = isset($_GET['id']) ? intval($_GET['id']) : intval($user_id);


if($target_id !== intval($user_id)) {
    echo "<script>alert('Ação não autorizada.'); window.location.href='../Paginas/usuario.php';</script>";
    exit;
}


$erro = $_GET['erro'] ?? '';
$sucesso = $_GET['sucesso'] ?? '';
$mensagem = '';

if($erro) {
    $mensagens_erro = [
        'senha_incorreta' => 'Senha atual incorreta!',
        'senhas_nao_coincidem' => 'A nova senha e a confirmação não coincidem!',
        'senha_curta' => 'Senha muito curta. Use ao menos 6 caracteres.',
        'usuario_nao_logado' => 'Usuário não está logado!',
        'usuario_nao_encontrado' => 'Usuário não encontrado!',
        'erro_banco' => 'Erro no banco de dados. Tente novamente.'
    ];
    $mensagem = $mensagens_erro[$erro] ?? 'Erro desconhecido';
    $tipo_mensagem = 'erro';
}

if($sucesso) {
    $mensagens_sucesso = [
        'senha_alterada' => 'Senha alterada com sucesso!'
    ];
    $mensagem = $mensagens_sucesso[$sucesso] ?? 'Ação realizada com sucesso!';
    $tipo_mensagem = 'sucesso';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Alterar Senha</title>

    <link rel="stylesheet" href="../Estilo/Register.css">
    
    <style>
        .mensagem-topo {
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .mensagem-erro {
            background-color: #ffebee;
            color: #c62828;
            border: 2px solid #f44336;
        }
        
        .mensagem-sucesso {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 2px solid #4caf50;
        }
        
        .fechar-mensagem {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            margin-left: 15px;
            float: right;
        }
    </style>
</head>
<body>
    <main class="register-container">
        <form action="../banco/alterar_senha.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($target_id) ?>">
            
            <h1>Alterar Senha</h1>

            <?php if($mensagem): ?>
            <div class="mensagem-topo mensagem-<?= $tipo_mensagem ?>">
                <?= htmlspecialchars($mensagem) ?>
                <button class="fechar-mensagem" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php endif; ?>

            <div class="input-box">
                <input id="senha_atual" name="senha_atual" placeholder="Senha Atual" type="password" required>
            </div>

            <div class="input-box">
                <input id="nova_senha" name="nova_senha" placeholder="Nova Senha" type="password" required>
            </div>

            <div class="input-box">
                <input id="confirm_senha" name="confirm_senha" placeholder="Confirmar Nova Senha" type="password" required>
            </div>

            <button type="submit" id="submit" name="submit" class="register">Salvar Nova Senha</button>

            <div class="register-link">
                <a href="usuario.php">Voltar para Usuário</a>
            </div>
        </form>
    </main>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const mensagem = document.querySelector('.mensagem-topo');
            if(mensagem) {
                setTimeout(() => {
                    mensagem.remove();
                }, 5000);
            }
        });
    </script>

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