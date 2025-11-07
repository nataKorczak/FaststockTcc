<?php
session_start();


$erro = $_GET['erro'] ?? '';
$mensagem_erro = '';

switch($erro) {
    case 'email':
        $mensagem_erro = 'Email não encontrado!';
        break;
    case 'senha':
        $mensagem_erro = 'Senha incorreta!';
        break;
    case 'campos_vazios':
        $mensagem_erro = 'Preencha todos os campos!';
        break;
    case 'erro_bd':
        $mensagem_erro = 'Erro no servidor. Tente novamente.';
        break;
}


if(isset($_SESSION['email'])) {
    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../Estilo/Login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Login - FastStock</title>
    
    <style>
        .mensagem-erro {
            background-color: #ffebee;
            color: #c62828;
            border: 2px solid #f44336;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }
        
        .mensagem-sucesso {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 2px solid #4caf50;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
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
    <main class="login-container">
        <form action="../banco/testelogin.php" method="POST">
            <h1>Login</h1>
            
            <?php if($mensagem_erro): ?>
            <div class="mensagem-erro">
                <button class="fechar-mensagem" onclick="this.parentElement.remove()">×</button>
                <?= htmlspecialchars($mensagem_erro) ?>
            </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['sucesso_cadastro'])): ?>
            <div class="mensagem-sucesso">
                <button class="fechar-mensagem" onclick="this.parentElement.remove()">×</button>
                <?= htmlspecialchars($_SESSION['sucesso_cadastro']) ?>
            </div>
            <?php unset($_SESSION['sucesso_cadastro']); ?>
            <?php endif; ?>

            <div class="input-box">
                <input id="email" name="email" placeholder="E-mail" type="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <i class="bx bxs-user"></i>
            </div>
            
            <div class="input-box">
                <input id="senha" name="senha" placeholder="Senha" type="password" required>
                <i class="bx bxs-lock-alt"></i>
            </div>



            <button type="submit" name="submit" class="login">Entrar</button>

            <div class="register-link">
                <p>Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
            </div>
        </form>
    </main>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const mensagens = document.querySelectorAll('.mensagem-erro, .mensagem-sucesso');
                mensagens.forEach(msg => {
                    if (msg.parentNode) {
                        msg.remove();
                    }
                });
            }, 5000);
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