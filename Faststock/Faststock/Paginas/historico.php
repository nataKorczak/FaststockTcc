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
$nome_usuario = $_SESSION['nome'] ?? 'Usuário';

include_once('../banco/bancoProdutos/configuracoes.php');

$historico = [];
$total_entradas = 0;
$total_saidas = 0;
$total_ajustes = 0;
$total_remocoes = 0;

try {
    $sql = "SELECT 
                hm.id,
                hm.nome_produto,
                hm.codigo_barras,
                hm.categoria,
                hm.unidade,
                hm.quantidade_anterior,
                hm.quantidade_nova,
                hm.tipo_movimentacao,
                hm.fornecedor,
                hm.usuario_nome,
                hm.data_movimentacao,
                hm.observacao,
                hm.imagem_produto,
                p.imagem as produto_imagem_atual
            FROM historico_movimentacoes hm
            LEFT JOIN produto p ON hm.codigo_barras = p.codigoBarra
            ORDER BY hm.data_movimentacao DESC 
            LIMIT 100";
    
    $result = $conexao->query($sql);
    if ($result) {
        while($row = $result->fetch_assoc()) {
            if (!empty($row['imagem_produto']) && file_exists($row['imagem_produto'])) {
                $row['imagem'] = $row['imagem_produto'];
            } 
            else if (!empty($row['produto_imagem_atual']) && file_exists($row['produto_imagem_atual'])) {
                $row['imagem'] = $row['produto_imagem_atual'];
            }
            else {
                $placeholder_path = '../Imagens/sem-imagem.jpg';
                if (file_exists($placeholder_path)) {
                    $row['imagem'] = $placeholder_path;
                } else {
                    $row['imagem'] = 'data:image/svg+xml;base64,' . base64_encode('
                        <svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
                            <rect width="100" height="100" fill="#f0f0f0"/>
                            <text x="50" y="55" text-anchor="middle" font-family="Arial" font-size="12" fill="#666">Sem Imagem</text>
                        </svg>
                    ');
                }
            }
            
            $historico[] = $row;
            
            switch($row['tipo_movimentacao']) {
                case 'ENTRADA': $total_entradas++; break;
                case 'SAIDA': $total_saidas++; break;
                case 'AJUSTE': $total_ajustes++; break;
                case 'REMOCAO': $total_remocoes++; break;
            }
        }
    }
} catch (Exception $e) {
    error_log("Erro ao carregar histórico: " . $e->getMessage());
    $erro_historico = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - FastStock</title>
    <link rel="stylesheet" href="../Estilo/Historico.css">
    
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

        .filtros {
            background-color: #2F4F4F;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filtros select, .filtros input {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background-color: white;
            color: #333;
            min-width: 150px;
        }

        .filtros button {
            background-color: #3F2605;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .filtros button:hover {
            background-color: #603813;
        }

        .sem-dados {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }

        .estatisticas {
            margin-top: 50px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .card-estatistica {
            background-color: #2F4F4F;
            padding: 15px;
            border-radius: 8px;
            color: white;
            min-width: 120px;
            text-align: center;
            flex: 1;
        }

        .card-entrada { border-left: 4px solid #28a745; }
        .card-saida { border-left: 4px solid #dc3545; }
        .card-ajuste { border-left: 4px solid #ffc107; }
        .card-remocao { border-left: 4px solid #6c757d; }

        .numero-estatistica {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #3F2605;
            color: white;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: #2F4F4F;
        }

        tr:hover {
            background-color: #3a5a5a;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-entrada { background-color: #28a745; color: white; }
        .badge-saida { background-color: #dc3545; color: white; }
        .badge-ajuste { background-color: #ffc107; color: #212529; }
        .badge-remocao { background-color: #6c757d; color: white; }

        .contador-filtros {
            background: #3F2605;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        
        .filtro-ativo {
            border-left: 4px solid #28a745 !important;
        }

        img {
            object-fit: cover;
            border-radius: 4px;
        }
        
        img[src*="broken"] {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
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
                <p class="texto_pagina">HISTÓRICO DE MOVIMENTAÇÃO</p>
            </div>

            <div class="estatisticas">
                <div class="card-estatistica card-entrada">
                    <span class="numero-estatistica"><?= $total_entradas ?></span>
                    <span>Entradas</span>
                </div>
                <div class="card-estatistica card-saida">
                    <span class="numero-estatistica"><?= $total_saidas ?></span>
                    <span>Saídas</span>
                </div>
                <div class="card-estatistica card-ajuste">
                    <span class="numero-estatistica"><?= $total_ajustes ?></span>
                    <span>Ajustes</span>
                </div>
                <div class="card-estatistica card-remocao">
                    <span class="numero-estatistica"><?= $total_remocoes ?></span>
                    <span>Remoções</span>
                </div>
                <div class="card-estatistica" style="border-left: 4px solid #17a2b8;">
                    <span class="numero-estatistica"><?= count($historico) ?></span>
                    <span>Total</span>
                </div>
            </div>

            <div class="filtros">
                <select id="filtroTipo">
                    <option value="">Todos os tipos</option>
                    <option value="ENTRADA">Entrada</option>
                    <option value="SAIDA">Saída</option>
                    <option value="AJUSTE">Ajuste</option>
                    <option value="REMOCAO">Remoção</option>
                </select>

                <select id="filtroCategoria">
                    <option value="">Todas as categorias</option>
                    <option value="Bebidas">Bebidas</option>
                    <option value="Alimentos">Alimentos</option>
                    <option value="Limpeza">Limpeza</option>
                    <option value="Higiene">Higiene</option>
                    <option value="Eletrônicos">Eletrônicos</option>
                    <option value="Outros">Outros</option>
                </select>

                <input type="date" id="filtroDataInicio" placeholder="Data início">
                <input type="date" id="filtroDataFim" placeholder="Data fim">

                <button onclick="limparFiltros()" style="background-color: #6c757d;">🗑️ Limpar</button>
            </div>

            <section id="historico-section">
                <?php if (isset($erro_historico)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h4>Erro ao carregar histórico</h4>
                        <p><?= htmlspecialchars($erro_historico) ?></p>
                        <p>Verifique se a tabela 'historico_movimentacoes' existe no banco de dados.</p>
                    </div>
                <?php endif; ?>
                
                <table id="tabelaHistorico">
                    <thead>
                        <tr>
                            <th>IMAGEM</th>
                            <th>PRODUTO</th>
                            <th>CATEGORIA</th>
                            <th>UNIDADE</th>
                            <th>QUANTIDADE</th>
                            <th>FORNECEDOR</th>
                            <th>DATA</th>
                            <th>TIPO</th>
                            <th>USUÁRIO</th>
                            <th>OBSERVAÇÃO</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabela">
                        <?php if (empty($historico)): ?>
                            <tr>
                                <td colspan="10" class="sem-dados">
                                    Nenhuma movimentação registrada
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historico as $movimento): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($movimento['imagem']) ?>" 
                                             alt="<?= htmlspecialchars($movimento['nome_produto']) ?>" 
                                             width="50" height="50" 
                                             style="object-fit: cover; border-radius: 4px;"
                                             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,<?= base64_encode('<svg width=\"50\" height=\"50\" xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"50\" height=\"50\" fill=\"#f0f0f0\"/><text x=\"25\" y=\"28\" text-anchor=\"middle\" font-family=\"Arial\" font-size=\"10\" fill=\"#666\">Sem Imagem</text></svg>') ?>'">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($movimento['nome_produto']) ?></strong>
                                        <?php if ($movimento['codigo_barras']): ?>
                                            <br><small style="color: #666;"><?= htmlspecialchars($movimento['codigo_barras']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($movimento['categoria']) ?></td>
                                    <td><?= htmlspecialchars($movimento['unidade']) ?></td>
                                    <td>
                                        <?php if ($movimento['tipo_movimentacao'] === 'REMOCAO'): ?>
                                            <span style="color: #dc3545; font-weight: bold;">
                                                REMOVIDO
                                            </span>
                                        <?php else: ?>
                                            <span style="color: <?= $movimento['quantidade_anterior'] < $movimento['quantidade_nova'] ? '#28a745' : '#dc3545' ?>; font-weight: bold;">
                                                <?= $movimento['quantidade_anterior'] ?> → <?= $movimento['quantidade_nova'] ?>
                                            </span>
                                            <?php if ($movimento['quantidade_anterior'] != $movimento['quantidade_nova']): ?>
                                                <br><small style="color: #666;">
                                                    <?php 
                                                    $diferenca = $movimento['quantidade_nova'] - $movimento['quantidade_anterior'];
                                                    echo ($diferenca > 0 ? '+' : '') . $diferenca;
                                                    ?>
                                                </small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($movimento['fornecedor'] ?? 'N/A') ?></td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($movimento['data_movimentacao'])) ?></small>
                                        <br><small style="color: #666;"><?= date('H:i', strtotime($movimento['data_movimentacao'])) ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $cores = [
                                            'ENTRADA' => 'badge-entrada',
                                            'SAIDA' => 'badge-saida', 
                                            'AJUSTE' => 'badge-ajuste',
                                            'REMOCAO' => 'badge-remocao'
                                        ];
                                        $nomes = [
                                            'ENTRADA' => 'Entrada',
                                            'SAIDA' => 'Saída',
                                            'AJUSTE' => 'Ajuste',
                                            'REMOCAO' => 'Remoção'
                                        ];
                                        ?>
                                        <span class="badge <?= $cores[$movimento['tipo_movimentacao']] ?>">
                                            <?= $nomes[$movimento['tipo_movimentacao']] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small style="color: #666;"><?= htmlspecialchars($movimento['usuario_nome']) ?></small>
                                    </td>
                                    <td>
                                        <small style="color: #666;"><?= htmlspecialchars($movimento['observacao']) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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

    <link rel="stylesheet" href="../Estilo/temaGlobal.css">
    <script src="../Configuracao/temaGlobal.js"></script>

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
        class FiltroHistorico {
            constructor() {
                this.dadosOriginais = <?= json_encode($historico) ?>;
                this.dadosFiltrados = [...this.dadosOriginais];
                this.filtrosAtivos = {
                    tipo: '',
                    categoria: '',
                    dataInicio: '',
                    dataFim: ''
                };
                this.init();
            }

            init() {
                this.configurarEventos();
                this.iniciarFiltrosLimpos();
            }

            configurarEventos() {
                document.getElementById('filtroTipo').addEventListener('change', () => this.aplicarFiltros());
                document.getElementById('filtroCategoria').addEventListener('change', () => this.aplicarFiltros());
                document.getElementById('filtroDataInicio').addEventListener('change', () => this.aplicarFiltros());
                document.getElementById('filtroDataFim').addEventListener('change', () => this.aplicarFiltros());
            }

            iniciarFiltrosLimpos() {
                document.getElementById('filtroTipo').value = '';
                document.getElementById('filtroCategoria').value = '';
                document.getElementById('filtroDataInicio').value = '';
                document.getElementById('filtroDataFim').value = '';
                
                this.aplicarFiltros();
                
                console.log('Filtros iniciados limpos - mostrando todos os registros');
            }

            aplicarFiltros() {
                this.mostrarLoading();
                
                this.filtrosAtivos = {
                    tipo: document.getElementById('filtroTipo').value,
                    categoria: document.getElementById('filtroCategoria').value,
                    dataInicio: document.getElementById('filtroDataInicio').value,
                    dataFim: document.getElementById('filtroDataFim').value
                };

                this.filtrarDados();
                
                setTimeout(() => {
                    this.esconderLoading();
                    this.mostrarResultados();
                }, 500);
            }

            filtrarDados() {
                this.dadosFiltrados = this.dadosOriginais.filter(item => {
                    return this.filtrarPorTipo(item) && 
                           this.filtrarPorCategoria(item) && 
                           this.filtrarPorData(item);
                });
            }

            filtrarPorTipo(item) {
                if (!this.filtrosAtivos.tipo) return true;
                return item.tipo_movimentacao === this.filtrosAtivos.tipo;
            }

            filtrarPorCategoria(item) {
                if (!this.filtrosAtivos.categoria) return true;
                return item.categoria === this.filtrosAtivos.categoria;
            }

            filtrarPorData(item) {
                if (!this.filtrosAtivos.dataInicio && !this.filtrosAtivos.dataFim) return true;
                
                const dataMovimentacao = new Date(item.data_movimentacao);
                const dataInicio = this.filtrosAtivos.dataInicio ? new Date(this.filtrosAtivos.dataInicio) : null;
                const dataFim = this.filtrosAtivos.dataFim ? new Date(this.filtrosAtivos.dataFim) : null;

                if (dataInicio && dataFim) {
                    return dataMovimentacao >= dataInicio && dataMovimentacao <= dataFim;
                } else if (dataInicio) {
                    return dataMovimentacao >= dataInicio;
                } else if (dataFim) {
                    return dataMovimentacao <= dataFim;
                }
                
                return true;
            }

            mostrarResultados() {
                const tbody = document.getElementById('corpoTabela');
                const totalResultados = document.querySelector('.card-estatistica:last-child .numero-estatistica');
                
                if (this.dadosFiltrados.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="sem-dados">
                                📭 Nenhuma movimentação encontrada com os filtros aplicados
                            </td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML = this.dadosFiltrados.map(item => this.criarLinhaTabela(item)).join('');
                }

                if (totalResultados) {
                    totalResultados.textContent = this.dadosFiltrados.length;
                }

                this.mostrarResumoFiltros();
            }

            criarLinhaTabela(item) {
                const cores = {
                    'ENTRADA': 'badge-entrada',
                    'SAIDA': 'badge-saida', 
                    'AJUSTE': 'badge-ajuste',
                    'REMOCAO': 'badge-remocao'
                };
                
                const nomes = {
                    'ENTRADA': 'Entrada',
                    'SAIDA': 'Saída',
                    'AJUSTE': 'Ajuste',
                    'REMOCAO': 'Remoção'
                };

                const diferenca = item.quantidade_nova - item.quantidade_anterior;
                const corQuantidade = diferenca > 0 ? '#28a745' : '#dc3545';
                const simboloQuantidade = diferenca > 0 ? '+' : '';

                return `
                    <tr>
                        <td>
                            <img src="${item.imagem}" 
                                 alt="${item.nome_produto}" 
                                 width="50" height="50" style="object-fit: cover; border-radius: 4px;"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml;base64,${btoa('<svg width="50" height="50" xmlns="http://www.w3.org/2000/svg"><rect width="50" height="50" fill="#f0f0f0"/><text x="25" y="28" text-anchor="middle" font-family="Arial" font-size="10" fill="#666">Sem Imagem</text></svg>')}''">
                        </td>
                        <td>
                            <strong>${this.escapeHtml(item.nome_produto)}</strong>
                            ${item.codigo_barras ? `<br><small style="color: #ffffffff;">${this.escapeHtml(item.codigo_barras)}</small>` : ''}
                        </td>
                        <td>${this.escapeHtml(item.categoria)}</td>
                        <td>${this.escapeHtml(item.unidade)}</td>
                        <td>
                            ${item.tipo_movimentacao === 'REMOCAO' ? 
                                `<span style="color: #dc3545; font-weight: bold;">REMOVIDO</span>` :
                                `<span style="color: ${corQuantidade}; font-weight: bold;">
                                    ${item.quantidade_anterior} → ${item.quantidade_nova}
                                </span>
                                ${item.quantidade_anterior != item.quantidade_nova ? 
                                    `<br><small style="color: #fffdfdff;">${simboloQuantidade}${diferenca}</small>` : ''}`
                            }
                        </td>
                        <td>${this.escapeHtml(item.fornecedor || 'N/A')}</td>
                        <td>
                            <small>${this.formatarData(item.data_movimentacao)}</small>
                            <br><small style="color: #ffffffff;">${this.formatarHora(item.data_movimentacao)}</small>
                        </td>
                        <td>
                            <span class="badge ${cores[item.tipo_movimentacao]}">
                                ${nomes[item.tipo_movimentacao]}
                            </span>
                        </td>
                        <td>
                            <small style="color: #ffffffff;">${this.escapeHtml(item.usuario_nome)}</small>
                        </td>
                        <td>
                            <small style="color: #ffffffff;">${this.escapeHtml(item.observacao)}</small>
                        </td>
                    </tr>
                `;
            }

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            formatarData(data) {
                return new Date(data).toLocaleDateString('pt-BR');
            }

            formatarHora(data) {
                return new Date(data).toLocaleTimeString('pt-BR', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            }

            mostrarResumoFiltros() {
                const filtrosAplicados = [];
                if (this.filtrosAtivos.tipo) filtrosAplicados.push(`Tipo: ${this.filtrosAtivos.tipo}`);
                if (this.filtrosAtivos.categoria) filtrosAplicados.push(`Categoria: ${this.filtrosAtivos.categoria}`);
                if (this.filtrosAtivos.dataInicio) filtrosAplicados.push(`De: ${this.filtrosAtivos.dataInicio}`);
                if (this.filtrosAtivos.dataFim) filtrosAplicados.push(`Até: ${this.filtrosAtivos.dataFim}`);

                console.log('Filtros aplicados:', filtrosAplicados.length > 0 ? filtrosAplicados : 'Nenhum filtro');
                console.log('Resultados:', this.dadosFiltrados.length, 'de', this.dadosOriginais.length, 'registros');
            }

            mostrarLoading() {
                console.log('Aplicando filtros...');
            }

            esconderLoading() {
                console.log('Filtros aplicados!');
            }
        }

        function aplicarFiltros() {
            if (window.filtroHistorico) {
                window.filtroHistorico.aplicarFiltros();
            }
        }

        function limparFiltros() {
            document.getElementById('filtroTipo').value = '';
            document.getElementById('filtroCategoria').value = '';
            document.getElementById('filtroDataInicio').value = '';
            document.getElementById('filtroDataFim').value = '';
            
            if (window.filtroHistorico) {
                window.filtroHistorico.aplicarFiltros();
            }
            
            alert('✅ Filtros limpos! Mostrando todos os registros.');
        }

        function exportarHistorico() {
            if (window.filtroHistorico) {
                const dados = window.filtroHistorico.dadosFiltrados;
                if (dados.length === 0) {
                    alert('Nenhum dado para exportar!');
                    return;
                }
                
                let csv = 'Produto,Categoria,Quantidade Anterior,Quantidade Nova,Tipo,Data,Usuário\n';
                dados.forEach(item => {
                    csv += `"${item.nome_produto}","${item.categoria}",${item.quantidade_anterior},${item.quantidade_nova},"${item.tipo_movimentacao}","${item.data_movimentacao}","${item.usuario_nome}"\n`;
                });
                
                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `historico_${new Date().toISOString().split('T')[0]}.csv`;
                a.click();
                
                alert(`📊 Exportados ${dados.length} registros para CSV!`);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            window.filtroHistorico = new FiltroHistorico();
            console.log('Sistema de filtros do histórico inicializado com filtros limpos!');
        });
    </script>
</body>
</html>