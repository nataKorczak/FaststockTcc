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

include_once('../banco/bancoProdutos/configuracoes.php');


$debug_file = __DIR__ . '/meu_debug.log';
$insert_file = __DIR__ . '/insert_debug.log';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_content = "=== " . date('Y-m-d H:i:s') . " ===\n";
    $log_content .= "Categoria recebida: " . ($_POST['categoria'] ?? 'NULL') . "\n";
    $log_content .= "Todos POST: " . print_r($_POST, true) . "\n";
    $log_content .= "==================\n\n";
    
    file_put_contents($debug_file, $log_content, FILE_APPEND);
    
    if ($_POST['acao'] === 'adicionar') {
        $insert_log = "INSERT - Categoria: " . ($_POST['categoria'] ?? 'NULL') . "\n";
        file_put_contents($insert_file, $insert_log, FILE_APPEND);
    }
}

function registrarMovimentacao($dados) {
    global $conexao;
    
    $stmt = $conexao->prepare("INSERT INTO historico_movimentacoes 
        (produto_id, nome_produto, codigo_barras, categoria, unidade, 
         quantidade_anterior, quantidade_nova, tipo_movimentacao, 
         fornecedor, usuario_id, usuario_nome, observacao, imagem_produto) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("issssiissssss",
        $dados['produto_id'],
        $dados['nome_produto'],
        $dados['codigo_barras'],
        $dados['categoria'],
        $dados['unidade'],
        $dados['quantidade_anterior'],
        $dados['quantidade_nova'],
        $dados['tipo_movimentacao'],
        $dados['fornecedor'],
        $dados['usuario_id'],
        $dados['usuario_nome'],
        $dados['observacao'],
        $dados['imagem_produto']
    );
    
    return $stmt->execute();
}

function uploadImagem($file) {
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['sucesso' => false, 'erro' => 'Erro no upload da imagem'];
    }

    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    $tamanhoMax = 15 * 1024 * 1024; 

    if (!in_array($file['type'], $tiposPermitidos)) {
        return ['sucesso' => false, 'erro' => 'Tipo de imagem não permitido. Use JPEG, PNG ou GIF.'];
    }

    if ($file['size'] > $tamanhoMax) {
        return ['sucesso' => false, 'erro' => 'Imagem muito grande. Máximo 10MB.'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nomeNovo = uniqid('produto_') . '.' . $ext;
    $pasta = "../uploads/";
    
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    
    $caminho = $pasta . $nomeNovo;
    
    if (move_uploaded_file($file['tmp_name'], $caminho)) {
        return ['sucesso' => true, 'caminho' => $caminho];
    } else {
        return ['sucesso' => false, 'erro' => 'Erro ao salvar imagem'];
    }
}


function testeInsertDireto($conexao) {
    $teste_sql = "INSERT INTO produto (nomeProduto, descricao, unidade, valorCompra, codigoBarra, categoria, quantidade, valorVendas, fornecedor) 
                  VALUES ('TESTE DIRETO', 'Teste direto', 'Un', 10.00, 'TESTE_DIRETO_123', 'Bebidas', 1, 15.00, 'Teste')";
    
    if ($conexao->query($teste_sql)) {
        $id = $conexao->insert_id;
        error_log("🎯 TESTE DIRETO - INSERT executado, ID: " . $id);
        
        
        $check_sql = "SELECT categoria FROM produto WHERE idproduto = " . $id;
        $result = $conexao->query($check_sql);
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            error_log("🎯 TESTE DIRETO - CATEGORIA NO BANCO: '" . $data['categoria'] . "'");
        }
        
        
        $conexao->query("DELETE FROM produto WHERE idproduto = " . $id);
    } else {
        error_log("❌ TESTE DIRETO - Erro: " . $conexao->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao'])) {
    if ($_GET['acao'] === 'carregar') {
        $sql = "SELECT * FROM produto ORDER BY nomeProduto";
        $result = $conexao->query($sql);
        
        if ($result) {
            $produtos = [];
            while($row = $result->fetch_assoc()) {
                $produtos[] = $row;
            }
            echo json_encode(['status' => 'sucesso', 'dados' => $produtos]);
        } else {
            echo json_encode(['status' => 'erro', 'msg' => 'Erro ao carregar produtos: ' . $conexao->error]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    error_log("🎯 ========== DEBUG INICIO ==========");
    error_log("📦 MÉTODO: " . $_SERVER['REQUEST_METHOD']);
    error_log("🔧 AÇÃO: " . ($_POST['acao'] ?? 'NÃO DEFINIDA'));
    error_log("📋 TODOS OS DADOS POST:");
    foreach ($_POST as $key => $value) {
        error_log("   {$key}: " . (is_array($value) ? print_r($value, true) : $value));
    }
    
    
    error_log("🔍 CATEGORIA RECEBIDA: " . ($_POST['categoria'] ?? 'NÃO ENVIADA'));
    error_log("🔍 TIPO DA CATEGORIA: " . gettype($_POST['categoria'] ?? 'NULL'));
    error_log("🔍 VALOR BRUTO DA CATEGORIA: " . bin2hex($_POST['categoria'] ?? ''));
    
    if (isset($_FILES['imagem'])) {
        error_log("🖼️ IMAGEM: " . ($_FILES['imagem']['name'] ?? 'Nenhuma'));
    }
    error_log("🎯 ========== DEBUG FIM ==========");
    
    $acao = $_POST['acao'] ?? 'adicionar';
    
    
    if ($acao === 'adicionar') {
        testeInsertDireto($conexao);
    }
    
    if ($acao === 'remover') {
        $codigoBarra = $_POST['codigoBarra'] ?? '';
        
        if (empty($codigoBarra)) {
            echo json_encode(['status' => 'erro', 'msg' => 'Código de barras não fornecido']);
            exit;
        }
        
        $stmt_select = $conexao->prepare("SELECT * FROM produto WHERE codigoBarra = ?");
        $stmt_select->bind_param("s", $codigoBarra);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();
        
        if ($result_select->num_rows > 0) {
            $produto = $result_select->fetch_assoc();
            
            $dados_historico = [
                'produto_id' => 0,
                'nome_produto' => $produto['nomeProduto'],
                'codigo_barras' => $produto['codigoBarra'],
                'categoria' => $produto['categoria'],
                'unidade' => $produto['unidade'],
                'quantidade_anterior' => $produto['quantidade'],
                'quantidade_nova' => 0,
                'tipo_movimentacao' => 'REMOCAO',
                'fornecedor' => $produto['fornecedor'],
                'usuario_id' => $_SESSION['user_id'] ?? 0,
                'usuario_nome' => $_SESSION['nome'] ?? 'Sistema',
                'observacao' => 'Produto removido do estoque',
                'imagem_produto' => $produto['imagem']
            ];
            
            registrarMovimentacao($dados_historico);
        }
        
        $stmt = $conexao->prepare("DELETE FROM produto WHERE codigoBarra = ?");
        $stmt->bind_param("s", $codigoBarra);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'sucesso', 'msg' => 'Produto removido com sucesso!']);
        } else {
            echo json_encode(['status' => 'erro', 'msg' => 'Erro ao remover produto: ' . $stmt->error]);
        }
        exit;
    }
    
    $nomeProduto = $_POST['nomeProduto'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $unidade = $_POST['unidade'] ?? '';
    $valorCompra = floatval($_POST['valorCompra'] ?? 0);
    $codigoBarra = $_POST['codigoBarra'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $quantidade = intval($_POST['quantidade'] ?? 0);
    $valorVenda = floatval($_POST['valorVenda'] ?? 0);
    $fornecedor = $_POST['fornecedor'] ?? '';
    $codigoOriginal = $_POST['codigoOriginal'] ?? '';

    
    error_log("🎯 ========== DEBUG PROCESSAMENTO ==========");
    error_log("📝 Nome: " . $nomeProduto);
    error_log("🏷️ Categoria (processada): " . $categoria);
    error_log("🔢 Quantidade: " . $quantidade);
    error_log("💰 Valor Venda: " . $valorVenda);
    error_log("💸 Valor Compra: " . $valorCompra);
    error_log("🎯 ========== FIM PROCESSAMENTO ==========");

    if (empty($nomeProduto) || empty($codigoBarra)) {
        echo json_encode(['status' => 'erro', 'msg' => 'Nome e código de barras são obrigatórios!']);
        exit;
    }

    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['name'] != "") {
        $resultadoUpload = uploadImagem($_FILES['imagem']);
        if (!$resultadoUpload['sucesso']) {
            echo json_encode(['status' => 'erro', 'msg' => $resultadoUpload['erro']]);
            exit;
        }
        $imagem = $resultadoUpload['caminho'];
    }

    if ($acao === 'adicionar') {
        
        $stmt_check = $conexao->prepare("SELECT codigoBarra FROM produto WHERE codigoBarra = ?");
        $stmt_check->bind_param("s", $codigoBarra);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'erro', 'msg' => 'Código de barras já existe!']);
            exit;
        }

       
        error_log("🎯 ========== DEBUG INSERT DETALHADO ==========");
        error_log("🔍 Categoria antes do bind: " . $categoria);
        error_log("🔍 Tipo da categoria: " . gettype($categoria));
        
        $stmt = $conexao->prepare("INSERT INTO produto (nomeProduto, descricao, unidade, valorCompra, codigoBarra, categoria, quantidade, valorVendas, fornecedor, imagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        
        if (!$stmt) {
            error_log("❌ ERRO NO PREPARE: " . $conexao->error);
            echo json_encode(['status' => 'erro', 'msg' => 'Erro no prepare: ' . $conexao->error]);
            exit;
        }
        
        $stmt->bind_param("sssdssdiss", $nomeProduto, $descricao, $unidade, $valorCompra, $codigoBarra, $categoria, $quantidade, $valorVenda, $fornecedor, $imagem);
        
    
        if (!$stmt) {
            error_log("❌ ERRO NO BIND: " . $stmt->error);
            echo json_encode(['status' => 'erro', 'msg' => 'Erro no bind: ' . $stmt->error]);
            exit;
        }
        
        if ($stmt->execute()) {
            error_log("✅ INSERT executado com sucesso");
            error_log("🔍 Último ID: " . $conexao->insert_id);
            
            
            $check_sql = "SELECT categoria, nomeProduto FROM produto WHERE idproduto = " . $conexao->insert_id;
            $check_result = $conexao->query($check_sql);
            
            if ($check_result && $check_result->num_rows > 0) {
                $inserted_data = $check_result->fetch_assoc();
                error_log("🔍 CATEGORIA NO BANCO: '" . $inserted_data['categoria'] . "'");
                error_log("🔍 NOME NO BANCO: '" . $inserted_data['nomeProduto'] . "'");
                
        
                $debug_sql = "SELECT * FROM produto WHERE idproduto = " . $conexao->insert_id;
                $debug_result = $conexao->query($debug_sql);
                if ($debug_result) {
                    $all_data = $debug_result->fetch_assoc();
                    error_log("🔍 TODOS OS DADOS INSERIDOS: " . print_r($all_data, true));
                }
            } else {
                error_log("❌ Não consegui verificar o dado inserido");
            }
            
            $dados_historico = [
                'produto_id' => 0,
                'nome_produto' => $nomeProduto,
                'codigo_barras' => $codigoBarra,
                'categoria' => $categoria,
                'unidade' => $unidade,
                'quantidade_anterior' => 0,
                'quantidade_nova' => $quantidade,
                'tipo_movimentacao' => 'ENTRADA',
                'fornecedor' => $fornecedor,
                'usuario_id' => $_SESSION['user_id'] ?? 0,
                'usuario_nome' => $_SESSION['nome'] ?? 'Sistema',
                'observacao' => 'Novo produto adicionado ao estoque',
                'imagem_produto' => $imagem
            ];
            
            registrarMovimentacao($dados_historico);
            
            echo json_encode(['status' => 'sucesso', 'msg' => 'Produto salvo com sucesso!']);
        } else {
            error_log("❌ ERRO NO EXECUTE: " . $stmt->error);
            echo json_encode(['status' => 'erro', 'msg' => 'Erro ao salvar produto: ' . $stmt->error]);
        }
        
        exit;
    }

    if ($acao === 'editar') {
        
        $stmt_old = $conexao->prepare("SELECT quantidade, nomeProduto, categoria, unidade, fornecedor, imagem FROM produto WHERE codigoBarra = ?");
        $stmt_old->bind_param("s", $codigoOriginal);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        $quantidade_antiga = 0;
        $produto_antigo = [];
        
        if ($result_old->num_rows > 0) {
            $produto_antigo = $result_old->fetch_assoc();
            $quantidade_antiga = $produto_antigo['quantidade'];
        }

        if (empty($imagem)) {
            $stmt_img = $conexao->prepare("SELECT imagem FROM produto WHERE codigoBarra = ?");
            $stmt_img->bind_param("s", $codigoOriginal);
            $stmt_img->execute();
            $result_img = $stmt_img->get_result();
            $dados_atuais = $result_img->fetch_assoc();
            $imagem = $dados_atuais['imagem'] ?? '';
        }
        

        error_log("🎯 ========== DEBUG ATUALIZAÇÃO ==========");
        error_log("📝 Query UPDATE: UPDATE produto SET nomeProduto=?, descricao=?, unidade=?, valorCompra=?, codigoBarra=?, categoria=?, quantidade=?, valorVendas=?, fornecedor=?, imagem=? WHERE codigoBarra=?");
        error_log("📋 Valores: ");
        error_log("   nomeProduto: " . $nomeProduto);
        error_log("   descricao: " . $descricao);
        error_log("   unidade: " . $unidade);
        error_log("   valorCompra: " . $valorCompra);
        error_log("   codigoBarra: " . $codigoBarra);
        error_log("   categoria: " . $categoria);
        error_log("   quantidade: " . $quantidade);
        error_log("   valorVendas: " . $valorVenda);
        error_log("   fornecedor: " . $fornecedor);
        error_log("   imagem: " . $imagem);
        error_log("   codigoOriginal: " . $codigoOriginal);
        error_log("🎯 ========== FIM ATUALIZAÇÃO ==========");

        $stmt = $conexao->prepare("UPDATE produto SET nomeProduto=?, descricao=?, unidade=?, valorCompra=?, codigoBarra=?, categoria=?, quantidade=?, valorVendas=?, fornecedor=?, imagem=? WHERE codigoBarra=?");
        $stmt->bind_param("sssdssdisss", $nomeProduto, $descricao, $unidade, $valorCompra, $codigoBarra, $categoria, $quantidade, $valorVenda, $fornecedor, $imagem, $codigoOriginal);

        if ($stmt->execute()) {
            
            $tipo_movimentacao = 'AJUSTE';
            if ($quantidade > $quantidade_antiga) {
                $tipo_movimentacao = 'ENTRADA';
            } elseif ($quantidade < $quantidade_antiga) {
                $tipo_movimentacao = 'SAIDA';
            }
            
            $dados_historico = [
                'produto_id' => 0,
                'nome_produto' => $nomeProduto,
                'codigo_barras' => $codigoBarra,
                'categoria' => $categoria,
                'unidade' => $unidade,
                'quantidade_anterior' => $quantidade_antiga,
                'quantidade_nova' => $quantidade,
                'tipo_movimentacao' => $tipo_movimentacao,
                'fornecedor' => $fornecedor,
                'usuario_id' => $_SESSION['user_id'] ?? 0,
                'usuario_nome' => $_SESSION['nome'] ?? 'Sistema',
                'observacao' => 'Produto atualizado no estoque',
                'imagem_produto' => $imagem
            ];
            
            registrarMovimentacao($dados_historico);
            
            echo json_encode(['status' => 'sucesso', 'msg' => 'Produto salvo com sucesso!']);
        } else {
            error_log("❌ Erro na execução do UPDATE: " . $stmt->error);
            echo json_encode(['status' => 'erro', 'msg' => 'Erro ao salvar produto: ' . $stmt->error]);
        }
        
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Estoque - FastStock</title>
    <link rel="stylesheet" href="../Estilo/Gerenciar_Estoque.css">
    
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
    <link rel="stylesheet" href="../Estilo/temaglobal.css">
    <script src="../Configuracao/temaglobal.js"></script>
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
                <p class="texto_pagina">CONTROLE DE ESTOQUE</p>
            </div>

            <div class="cabecario_adicionar">
                <p>Adicionar Produto</p>
            </div>

            <section class="formulario_produto">
                <div class="file-input-container">
                    <label for="imagem">Imagem do Produto</label>
                    <input type="file" id="imagem" accept="image/jpeg,image/png,image/gif">
                    <img id="preview" alt="Pré-visualização" width="100" style="display: none; margin-top: 10px; border-radius: 4px;">
                </div>

                <div>
                    <label for="NomeProduto">Nome Produto:*</label>
                    <input type="text" id="NomeProduto" required>
                </div>

                <div>
                    <label for="descricaoProduto">Descrição:*</label>
                    <input type="text" id="descricaoProduto" required>
                </div>

                <div>
                    <label for="unidadeProduto">Unidade:*</label>
                    <select id="unidadeProduto" required>
                        <option value="">Selecione</option>
                        <option value="Cx">Caixa (Cx)</option>
                        <option value="Fr">Fardo (Fr)</option>
                        <option value="Un">Unidade (Un)</option>
                        <option value="Lt">Litro (Lt)</option>
                        <option value="Sc">Saco (Sc)</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div>
                    <label for="valorCompra">Valor de Compra:*</label>
                    <input type="number" id="valorCompra" step="0.01" min="0" required>
                </div>

                <div>
                    <label for="codigoBarras">Código de Barras:*</label>
                    <input type="text" id="codigoBarras" required>
                </div>

                <div>
                    <label for="categoriaProduto">Categoria:*</label>
                    <select id="categoriaProduto" required>
                        <option value="">Selecione</option>
                        <option value="Bebidas">Bebidas</option>
                        <option value="Alimentos">Alimentos</option>
                        <option value="Limpeza">Limpeza</option>
                        <option value="Higiene">Higiene</option>
                        <option value="Eletrônicos">Eletrônicos</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div>
                    <label for="quantidadeProduto">Quantidade:*</label>
                    <input type="number" id="quantidadeProduto" min="0" required>
                </div>

                <div>
                    <label for="valorVenda">Valor de Venda:*</label>
                    <input type="number" id="valorVenda" step="0.01" min="0" required>
                </div>

                <div>
                    <label for="fornecedorProduto">Fornecedor:</label>
                    <input type="text" id="fornecedorProduto">
                </div>

                <div class="adicionar_btn">
                    <button id="btnAdicionar" type="button">ADICIONAR</button>
                </div>
            </section>

            <div class="cabecario_estoque">
                <p>Estoque Atual</p>
            </div>

            <section>
                <table id="tabelaEstoque">
                    <thead>
                        <tr>
                            <th>IMAGEM</th>
                            <th>NOME</th>
                            <th>CODIGO BARRAS</th>
                            <th>DESCRIÇÃO</th>
                            <th>CATEGORIA</th>
                            <th>UNIDADE</th>
                            <th>QUANTIDADE</th>
                            <th>VALOR COMPRA</th>
                            <th>VALOR VENDA</th>
                            <th>FORNECEDOR</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabela">
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 20px;">
                                Carregando produtos...
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
    <script src="../Configuracao/Gerenciar_Estoque.js"></script>

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
            console.log('Página Gerenciar Estoque carregada');
        });
    </script>
</body>
</html>