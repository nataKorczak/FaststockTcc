<?php

session_start();

if(isset($_POST['submit'])) {
    include_once('../banco/config.php');

    $nome = $_POST['nome'] ?? '';
    $emailregister = $_POST['emailregister'] ?? '';
    $apelido = $_POST['apelido'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $senharegister = $_POST['senharegister'] ?? '';
    $confirmsenha = $_POST['confirmsenha'] ?? '';

    $erros = [];


    if(empty($nome)) {
        $erros[] = "Nome é obrigatório!";
    } elseif(strlen($nome) < 2) {
        $erros[] = "Nome deve ter pelo menos 2 caracteres!";
    } elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nome)) {
        $erros[] = "Nome deve conter apenas letras e espaços!";
    }

    if(empty($emailregister)) {
        $erros[] = "Email é obrigatório!";
    } elseif(!filter_var($emailregister, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Formato de email inválido!";
    } else {

        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $emailregister);
        $stmt->execute();
        if($stmt->get_result()->num_rows > 0) {
            $erros[] = "Este email já está cadastrado!";
        }
    }

    if(empty($apelido)) {
        $erros[] = "Username é obrigatório!";
    } elseif(strlen($apelido) < 3) {
        $erros[] = "Username deve ter pelo menos 3 caracteres!";
    } elseif(!preg_match("/^[a-zA-Z0-9_]+$/", $apelido)) {
        $erros[] = "Username deve conter apenas letras, números e underline!";
    }

    if(empty($telefone)) {
        $erros[] = "Telefone é obrigatório!";
    } else {
      
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        
      
        if(strlen($telefone_limpo) < 10 || strlen($telefone_limpo) > 11) {
            $erros[] = "Telefone deve ter 10 ou 11 dígitos!";
        } elseif(!preg_match('/^[0-9]{10,11}$/', $telefone_limpo)) {
            $erros[] = "Formato de telefone inválido!";
        } else {
           
            $telefone = $telefone_limpo;
        }
    }

   
    if(empty($senharegister)) {
        $erros[] = "Senha é obrigatória!";
    } elseif(strlen($senharegister) < 6) {
        $erros[] = "Senha deve ter pelo menos 6 caracteres!";
    } elseif(!preg_match('/[A-Z]/', $senharegister)) {
        $erros[] = "Senha deve conter pelo menos uma letra maiúscula!";
    } elseif(!preg_match('/[a-z]/', $senharegister)) {
        $erros[] = "Senha deve conter pelo menos uma letra minúscula!";
    } elseif(!preg_match('/[0-9]/', $senharegister)) {
        $erros[] = "Senha deve conter pelo menos um número!";
    }


    if($senharegister !== $confirmsenha) {
        $erros[] = "As senhas não coincidem!";
    }


    if(empty($erros)) {
        $senha_criptografada = password_hash($senharegister, PASSWORD_DEFAULT);

        $stmt = $conexao->prepare("INSERT INTO usuarios(nome,email,username,telefone,senha,confirmarsenha) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome, $emailregister, $apelido, $telefone, $senha_criptografada, $senha_criptografada);
        
        if($stmt->execute()) {
            $_SESSION['sucesso_cadastro'] = "Cadastro realizado com sucesso!";
            header('Location: login.php');
            exit;
        } else {
            $erros[] = "Erro ao cadastrar: " . $conexao->error;
        }
    }


    if(!empty($erros)) {
        $_SESSION['erros_cadastro'] = $erros;
        $_SESSION['dados_form'] = [
            'nome' => $nome,
            'emailregister' => $emailregister,
            'apelido' => $apelido,
            'telefone' => $telefone
        ];
        header('Location: register.php');
        exit;
    }
}


$dados_form = $_SESSION['dados_form'] ?? [];
$erros = $_SESSION['erros_cadastro'] ?? [];

unset($_SESSION['erros_cadastro']);
unset($_SESSION['dados_form']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Estilo/Register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Registrar</title>
    
    <style>
        .mensagem-erro {
            background-color: #ffebee;
            color: #c62828;
            border: 2px solid #f44336;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .mensagem-sucesso {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 2px solid #4caf50;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .lista-erros {
            margin: 0;
            padding-left: 20px;
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
        <form action="register.php" method="POST" onsubmit="return formatarTelefoneAntesEnvio()">
            <h1>Cadastrar-se</h1>

 
            <?php if(!empty($erros)): ?>
            <div class="mensagem-erro">
                <button class="fechar-mensagem" onclick="this.parentElement.remove()">×</button>
                <strong>Corrija os seguintes erros:</strong>
                <ul class="lista-erros">
                    <?php foreach($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
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
                <input id="nome" name="nome" placeholder="Nome Completo" type="text" 
                       value="<?= htmlspecialchars($dados_form['nome'] ?? '') ?>" required>
            </div>

            <div class="input-box">
                <input id="emailregister" name="emailregister" placeholder="E-mail" type="email"
                       value="<?= htmlspecialchars($dados_form['emailregister'] ?? '') ?>" required>
            </div>

            <div class="input-box">
                <input id="apelido" name="apelido" placeholder="Username" type="text"
                       value="<?= htmlspecialchars($dados_form['apelido'] ?? '') ?>" required>
            </div>
        
            <div class="input-box">
                <input id="telefone" name="telefone" placeholder="Telefone (11) 99999-9999" type="tel"
                       value="<?= htmlspecialchars($dados_form['telefone'] ?? '') ?>" required>
            </div>

            <div class="input-box">
                <input id="senharegister" name="senharegister" placeholder="Senha" type="password" required>
                <small style="color: #ffffffff; font-size: 12px; display: block; margin-top: 5px;">
                    Mínimo 6 caracteres, com letras maiúsculas, minúsculas e números
                </small>
            </div>

            <div class="input-box">
                <input id="confirmsenha" name="confirmsenha" placeholder="Confirmar Senha" type="password" required>
            </div>

            <button type="submit" id="submit" name="submit" class="register">Cadastrar-se</button>

            <div class="register-link">
                <a href="login.php">Voltar a tela de login</a>
            </div>
        </form>
    </main>

    <script>

        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                if (value.length <= 2) {
                    value = value.replace(/^(\d{0,2})/, '($1');
                } else if (value.length <= 6) {
                    value = value.replace(/^(\d{2})(\d{0,4})/, '($1) $2');
                } else if (value.length <= 10) {
                    value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                } else {
                    value = value.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                }
                e.target.value = value;
            }
        });


        function formatarTelefoneAntesEnvio() {
            const telefoneInput = document.getElementById('telefone');
            telefoneInput.value = telefoneInput.value.replace(/\D/g, '');
            return true;
        }


        document.getElementById('senharegister').addEventListener('input', function(e) {
            const senha = e.target.value;
            const forca = calcularForcaSenha(senha);
            atualizarIndicadorForcaSenha(forca);
        });

        function calcularForcaSenha(senha) {
            let forca = 0;
            
            if (senha.length >= 6) forca++;
            if (/[A-Z]/.test(senha)) forca++;
            if (/[a-z]/.test(senha)) forca++;
            if (/[0-9]/.test(senha)) forca++;
            if (/[^A-Za-z0-9]/.test(senha)) forca++;
            
            return forca;
        }

        function atualizarIndicadorForcaSenha(forca) {

            console.log('Força da senha:', forca);
        }


        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const mensagens = document.querySelectorAll('.mensagem-erro, .mensagem-sucesso');
                mensagens.forEach(msg => msg.remove());
            }, 8000);
        });
    </script>
</body>
</html>