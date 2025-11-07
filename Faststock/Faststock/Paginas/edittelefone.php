<?php
if(!empty($_GET['id'])) {
    include_once('../banco/config.php');

    $id = $_GET['id'];
    
    $sqlSelect = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conexao->prepare($sqlSelect);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $telefone = $user_data['telefone'] ?? ''; 
    } else {
        echo "Usuário não encontrado!";
        exit;
    }
} else {
    echo "Erro: ID não fornecido!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Estilo/Register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Editar Telefone - FastStock</title>
    
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
        
        .register-container {
            width: 420px;
            background-color: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            border-radius: 10px;
            color: #ffff;
            padding: 30px 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>
    <main class="register-container">
        <form action="../banco/update_user.php" method="POST" onsubmit="return formatarTelefoneAntesEnvio()">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <h1>Editar Telefone</h1>

            <?php 

            $erro = $_GET['erro'] ?? '';
            $sucesso = $_GET['sucesso'] ?? '';
            $mensagem = '';
            
            if($erro) {
                $mensagens_erro = [
                    'telefone_vazio' => 'Telefone é obrigatório!',
                    'telefone_invalido' => 'Telefone inválido!',
                    'erro_banco' => 'Erro no banco de dados. Tente novamente.'
                ];
                $mensagem = $mensagens_erro[$erro] ?? 'Erro desconhecido';
                $tipo_mensagem = 'erro';
            }
            
            if($sucesso) {
                $mensagens_sucesso = [
                    'telefone_alterado' => 'Telefone alterado com sucesso!'
                ];
                $mensagem = $mensagens_sucesso[$sucesso] ?? 'Ação realizada com sucesso!';
                $tipo_mensagem = 'sucesso';
            }
            ?>

            <?php if($mensagem): ?>
            <div class="mensagem-topo mensagem-<?= $tipo_mensagem ?>">
                <?= htmlspecialchars($mensagem) ?>
                <button class="fechar-mensagem" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php endif; ?>

            <div class="input-box">
                <input id="telefone" name="telefone" placeholder="Telefone" type="tel" 
                       value="<?= htmlspecialchars($telefone) ?>" required>
            </div>

            <button type="submit" id="submit" name="submit" class="register">Atualizar</button>

            <div class="register-link">
                <a href="javascript:history.back()">Cancelar</a>
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


        document.addEventListener('DOMContentLoaded', function() {

            document.getElementById('telefone').focus();
            

            setTimeout(() => {
                const mensagens = document.querySelectorAll('.mensagem-topo');
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