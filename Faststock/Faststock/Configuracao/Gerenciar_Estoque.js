let estoque = [];
let indiceEditando = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Iniciando Gerenciar Estoque...');
    carregarEstoque();

    document.getElementById('btnAdicionar').addEventListener('click', adicionarProduto);
});

async function carregarEstoque() {
    try {
        console.log('📦 Carregando estoque...');
        const resposta = await fetch('gerenciar_Estoque.php?acao=carregar');
        
        if (!resposta.ok) {
            throw new Error('Erro na resposta do servidor: ' + resposta.status);
        }
        
        const resultado = await resposta.json();
        console.log('✅ Resposta do servidor:', resultado);
        
        if(resultado.status === 'sucesso') {
            estoque = resultado.dados;
            console.log('📊 Produtos carregados:', estoque);
            atualizarTabela();
        } else {
            alert('Erro ao carregar estoque: ' + resultado.msg);
        }
    } catch (erro) {
        console.error('❌ Erro ao carregar estoque:', erro);
    }
}

document.getElementById('imagem').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    const arquivo = e.target.files[0];
    
    if (arquivo) {
        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (!tiposPermitidos.includes(arquivo.type)) {
            alert('Formato de imagem não permitido! Use JPEG, PNG ou GIF.');
            e.target.value = "";
            preview.src = "";
            preview.style.display = 'none';
            return;
        }
        
        if (arquivo.size > 15 * 1024 * 1024) {
            alert('Imagem muito grande! Máximo: 15MB');
            e.target.value = "";
            preview.src = "";
            preview.style.display = 'none';
            return;
        }
        
        preview.src = URL.createObjectURL(arquivo);
        preview.style.display = 'block';
    } else {
        preview.src = "";
        preview.style.display = 'none';
    }
});

async function adicionarProduto() {
    console.log('🟢 Iniciando adição de produto...');
    
    
    const nome = document.getElementById('NomeProduto').value.trim();
    const unidade = document.getElementById('unidadeProduto').value;
    const descricao = document.getElementById('descricaoProduto').value.trim();
    const categoriaSelect = document.getElementById('categoriaProduto');
    const categoria = categoriaSelect.value;
    const quantidade = parseInt(document.getElementById('quantidadeProduto').value);
    const precoVenda = parseFloat(document.getElementById('valorVenda').value);
    const precoCompra = parseFloat(document.getElementById('valorCompra').value);
    const fornecedor = document.getElementById('fornecedorProduto').value.trim();
    const codigoBarra = document.getElementById('codigoBarras').value.trim();
    const imagemInput = document.getElementById('imagem').files[0];

    
    console.log('🔍 === DEBUG CATEGORIA ===');
    console.log('📍 Valor da categoria (value):', categoria);
    console.log('📍 Elemento select completo:', categoriaSelect);
    console.log('📍 Índice selecionado:', categoriaSelect.selectedIndex);
    console.log('📍 Opção selecionada:', categoriaSelect.options[categoriaSelect.selectedIndex]);
    console.log('📍 Texto da opção selecionada:', categoriaSelect.options[categoriaSelect.selectedIndex]?.text);
    console.log('📍 HTML do select:', categoriaSelect.outerHTML);
    
    
    console.log('📍 Todas as opções disponíveis:');
    for (let i = 0; i < categoriaSelect.options.length; i++) {
        console.log(`  [${i}] value: "${categoriaSelect.options[i].value}", text: "${categoriaSelect.options[i].text}"`);
    }
    console.log('🔍 === FIM DEBUG CATEGORIA ===');

    console.log('📋 Dados completos do produto:', {
        nome, 
        unidade, 
        descricao, 
        categoria, 
        quantidade, 
        precoVenda, 
        precoCompra, 
        fornecedor, 
        codigoBarra
    });

    
    if (!nome) {
        alert("Nome do produto é obrigatório!");
        document.getElementById('NomeProduto').focus();
        return;
    }
    
    if (!codigoBarra) {
        alert("Código de barras é obrigatório!");
        document.getElementById('codigoBarras').focus();
        return;
    }
    
    if (!descricao) {
        alert("Descrição é obrigatória!");
        document.getElementById('descricaoProduto').focus();
        return;
    }
    
    if (!categoria) {
        alert("Categoria é obrigatória!");
        document.getElementById('categoriaProduto').focus();
        return;
    }
    
    if (!unidade) {
        alert("Unidade é obrigatória!");
        document.getElementById('unidadeProduto').focus();
        return;
    }
    
    if (isNaN(quantidade) || quantidade < 0) {
        alert("Quantidade deve ser um número válido!");
        document.getElementById('quantidadeProduto').focus();
        return;
    }
    
    if (isNaN(precoCompra) || precoCompra < 0) {
        alert("Valor de compra deve ser um número válido!");
        document.getElementById('valorCompra').focus();
        return;
    }
    
    if (isNaN(precoVenda) || precoVenda < 0) {
        alert("Valor de venda deve ser um número válido!");
        document.getElementById('valorVenda').focus();
        return;
    }

    if (quantidade < 5) {
        if (!confirm("Estoque abaixo do mínimo recomendado (5 unidades). Deseja continuar mesmo assim?")) {
            return;
        }
    }

    
    const formData = new FormData();
    formData.append('nomeProduto', nome);
    formData.append('unidade', unidade);
    formData.append('descricao', descricao);
    formData.append('categoria', categoria);
    formData.append('quantidade', quantidade);
    formData.append('valorVenda', precoVenda);
    formData.append('valorCompra', precoCompra);
    formData.append('fornecedor', fornecedor);
    formData.append('codigoBarra', codigoBarra);
    formData.append('acao', indiceEditando === null ? 'adicionar' : 'editar');
    
    if (indiceEditando !== null) {
        formData.append('codigoOriginal', estoque[indiceEditando].codigoBarra);
    }
    
    if (imagemInput) {
        formData.append('imagem', imagemInput);
    }

    
    console.log('📤 Enviando dados para o servidor...');
    console.log('📍 Categoria sendo enviada:', categoria);
    
    
    console.log('📍 Conteúdo completo do FormData:');
    for (let pair of formData.entries()) {
        console.log(`  ${pair[0]}: ${pair[1]}`);
    }

    try {
        console.log('🔄 Fazendo requisição para o servidor...');
        const resposta = await fetch('gerenciar_Estoque.php', { 
            method: 'POST', 
            body: formData
        });

        if (!resposta.ok) {
            throw new Error('Erro HTTP: ' + resposta.status);
        }

        const resultado = await resposta.json();
        console.log('✅ Resposta do servidor:', resultado);

        if (resultado.status === 'erro') {
            alert('Erro: ' + resultado.msg);
            return;
        }

        console.log('🔄 Recarregando estoque após sucesso...');
        await carregarEstoque();
        limparCampos();
        
        alert(indiceEditando === null ? '✅ Produto adicionado com sucesso!' : '✅ Produto atualizado com sucesso!');
        
    } catch (erro) {
        console.error('❌ Erro detalhado:', erro);
        alert("❌ Erro ao salvar produto no servidor! Verifique o console para detalhes.");
    }
}

function atualizarTabela() {
    const corpo = document.getElementById('corpoTabela');
    corpo.innerHTML = '';

    console.log('🔄 Atualizando tabela com', estoque.length, 'produtos');

    if (estoque.length === 0) {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 11;
        td.textContent = 'Nenhum produto cadastrado';
        td.style.textAlign = 'center';
        td.style.padding = '20px';
        td.style.color = '#666';
        tr.appendChild(td);
        corpo.appendChild(tr);
        return;
    }

    estoque.forEach((produto, index) => {
        const tr = document.createElement('tr');

        
        console.log(`📊 Produto ${index + 1} na tabela:`, produto);
        console.log(`📍 Categoria do produto ${index + 1}:`, produto.categoria);
        console.log(`📍 Tipo da categoria:`, typeof produto.categoria);

   
        const tdImagem = document.createElement('td');
        const img = document.createElement('img');
        if (produto.imagem) {
            img.src = produto.imagem;
        } else {
            img.src = '../Imagens/sem-imagem.jpg';
        }
        img.width = 60;
        img.height = 60;
        img.style.objectFit = 'cover';
        img.style.borderRadius = '4px';
        img.alt = produto.nomeProduto || 'Produto sem imagem';
        tdImagem.appendChild(img);
        tr.appendChild(tdImagem);

        
        const formatarPreco = (preco) => {
            return 'R$ ' + parseFloat(preco || 0).toFixed(2).replace('.', ',');
        };

       
        const dados = [
            produto.nomeProduto || '',
            produto.codigoBarra || '',
            produto.descricao || '',
            produto.categoria || '❌ SEM CATEGORIA',
            produto.unidade || '',
            produto.quantidade || 0,
            formatarPreco(produto.valorCompra),
            formatarPreco(produto.valorVendas || produto.valorVenda),
            produto.fornecedor || ''
        ];

        console.log(`📍 Dados para linha ${index + 1}:`, dados);

        dados.forEach((valor, colIndex) => {
            const td = document.createElement('td');
            td.textContent = valor;
            
          
            if (colIndex === 5 && produto.quantidade < 5) {
                td.style.color = '#ff4444';
                td.style.fontWeight = 'bold';
            }
            
            // Destacar categoria problemática
            if (colIndex === 3 && (valor === 'o' || valor === '❌ SEM CATEGORIA')) {
                td.style.backgroundColor = '#ffebee';
                td.style.color = '#c62828';
                td.style.fontWeight = 'bold';
            }
            
            tr.appendChild(td);
        });

        // Ações
        const tdAcoes = document.createElement('td');
        tdAcoes.style.display = 'flex';
        tdAcoes.style.gap = '5px';
        tdAcoes.style.justifyContent = 'center';

        const btnEditar = document.createElement('button');
        btnEditar.textContent = 'Editar';
        btnEditar.className = 'btn-editar';
        btnEditar.onclick = () => editarProduto(index);

        const btnRemover = document.createElement('button');
        btnRemover.textContent = 'Remover';
        btnRemover.className = 'btn-remover';
        btnRemover.onclick = () => removerProduto(index);

        tdAcoes.appendChild(btnEditar);
        tdAcoes.appendChild(btnRemover);
        tr.appendChild(tdAcoes);

        corpo.appendChild(tr);
    });
    
    console.log('✅ Tabela atualizada com sucesso!');
}

function editarProduto(index) {
    const produto = estoque[index];
    console.log('✏️ Editando produto:', produto);
    console.log('📍 Categoria do produto para edição:', produto.categoria);
    
    document.getElementById('NomeProduto').value = produto.nomeProduto || '';
    document.getElementById('unidadeProduto').value = produto.unidade || '';
    document.getElementById('descricaoProduto').value = produto.descricao || '';
    document.getElementById('categoriaProduto').value = produto.categoria || '';
    document.getElementById('quantidadeProduto').value = produto.quantidade || '';
    document.getElementById('valorVenda').value = produto.valorVendas || produto.valorVenda || '';
    document.getElementById('valorCompra').value = produto.valorCompra || '';
    document.getElementById('codigoBarras').value = produto.codigoBarra || '';
    document.getElementById('fornecedorProduto').value = produto.fornecedor || '';
    
    const preview = document.getElementById('preview');
    if (produto.imagem) {
        preview.src = produto.imagem;
        preview.style.display = 'block';
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }

    indiceEditando = index;
    document.getElementById('btnAdicionar').textContent = "Salvar Edição";
    
    console.log('📍 Valor definido no select de categoria:', document.getElementById('categoriaProduto').value);
    
    document.querySelector('.formulario_produto').scrollIntoView({ behavior: 'smooth' });
}

async function removerProduto(index) {
    const produto = estoque[index];
    
    if (!confirm(`Tem certeza que deseja remover o produto "${produto.nomeProduto}"?`)) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('acao', 'remover');
        formData.append('codigoBarra', produto.codigoBarra);

        console.log('🗑️ Removendo produto:', produto.codigoBarra);

        const resposta = await fetch('gerenciar_Estoque.php', {
            method: 'POST',
            body: formData
        });

        const resultado = await resposta.json();

        if (resultado.status === 'sucesso') {
            await carregarEstoque();
            alert('✅ Produto removido com sucesso!');
        } else {
            alert('❌ Erro ao remover produto: ' + resultado.msg);
        }
    } catch (erro) {
        console.error('❌ Erro ao remover:', erro);
        alert('❌ Erro ao remover produto do servidor!');
    }
}

function limparCampos() {
    console.log('🧹 Limpando campos do formulário...');
    
    document.getElementById('NomeProduto').value = '';
    document.getElementById('unidadeProduto').value = '';
    document.getElementById('descricaoProduto').value = '';
    document.getElementById('categoriaProduto').value = '';
    document.getElementById('quantidadeProduto').value = '';
    document.getElementById('valorVenda').value = '';
    document.getElementById('valorCompra').value = '';
    document.getElementById('codigoBarras').value = '';
    document.getElementById('fornecedorProduto').value = '';
    document.getElementById('imagem').value = '';
    
    const preview = document.getElementById('preview');
    preview.src = '';
    preview.style.display = 'none';
    
    indiceEditando = null;
    document.getElementById('btnAdicionar').textContent = "Adicionar";
    
    console.log('✅ Campos limpos!');
}


const estilo = `
    .btn-editar {
        background: #4d281d;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .btn-editar:hover {
        background: #603813;
    }
    
    .btn-remover {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .btn-remover:hover {
        background: #c82333;
    }
`;

const styleSheet = document.createElement("style");
styleSheet.innerText = estilo;
document.head.appendChild(styleSheet);

console.log('🎯 Gerenciar Estoque JS carregado com debug!');