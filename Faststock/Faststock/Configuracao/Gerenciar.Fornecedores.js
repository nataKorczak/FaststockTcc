let fornecedores = [];
let indiceEditando = null;


document.addEventListener('DOMContentLoaded', function() {
    console.log('Iniciando Gerenciar Fornecedores...');
    carregarFornecedores();
    

    document.getElementById('BtnAdicionar').addEventListener('click', AdicionarFornecedores);
    

    configurarMascaras();
});


function configurarMascaras() {

    document.getElementById('cnpj').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 14) {
            if (value.length <= 2) {
                value = value.replace(/^(\d{0,2})/, '$1');
            } else if (value.length <= 5) {
                value = value.replace(/^(\d{2})(\d{0,3})/, '$1.$2');
            } else if (value.length <= 8) {
                value = value.replace(/^(\d{2})(\d{3})(\d{0,3})/, '$1.$2.$3');
            } else if (value.length <= 12) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4})/, '$1.$2.$3/$4');
            } else {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5');
            }
            e.target.value = value;
        }
    });


    document.getElementById('cep').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 8) {
            if (value.length <= 5) {
                value = value.replace(/^(\d{0,5})/, '$1');
            } else {
                value = value.replace(/^(\d{5})(\d{0,3})/, '$1-$2');
            }
            e.target.value = value;
        }
    });

    document.getElementById('numero').addEventListener('input', function(e) {
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
}


async function carregarFornecedores() {
    try {
        console.log('Carregando fornecedores...');
        const resposta = await fetch('gerenciar_fornecedores.php?acao=carregar');
        
        if (!resposta.ok) {
            throw new Error('Erro na resposta do servidor: ' + resposta.status);
        }
        
        const resultado = await resposta.json();
        console.log('Resposta do servidor:', resultado);
        
        if(resultado.status === 'sucesso') {
            fornecedores = resultado.dados;
            atualizarTabela();
        } else {
            alert('Erro ao carregar fornecedores: ' + resultado.msg);
        }
    } catch (erro) {
        console.error('Erro ao carregar fornecedores:', erro);
        alert('Erro ao carregar fornecedores do servidor! Verifique o console.');
    }
}


async function AdicionarFornecedores() {
    console.log('Iniciando adição de fornecedor...');
    
    let cnpj = document.getElementById("cnpj").value.trim();
    let razaoSocial = document.getElementById("nome").value.trim();
    let cep = document.getElementById("cep").value.trim();
    let endereco = document.getElementById("endereco").value.trim();
    let contato = document.getElementById("numero").value.trim();
    let descricao = document.getElementById("descricao").value.trim();

    console.log('Dados do fornecedor:', {
        cnpj, razaoSocial, cep, endereco, contato, descricao
    });


    if (!cnpj) {
        alert("CNPJ é obrigatório!");
        document.getElementById('cnpj').focus();
        return;
    }
    
    if (!razaoSocial) {
        alert("Razão Social é obrigatória!");
        document.getElementById('nome').focus();
        return;
    }
    
    if (!cep) {
        alert("CEP é obrigatório!");
        document.getElementById('cep').focus();
        return;
    }
    
    if (!endereco) {
        alert("Endereço é obrigatório!");
        document.getElementById('endereco').focus();
        return;
    }
    
    if (!contato) {
        alert("Contato é obrigatório!");
        document.getElementById('numero').focus();
        return;
    }
    
    if (!descricao) {
        alert("Descrição é obrigatória!");
        document.getElementById('descricao').focus();
        return;
    }


    let regexCNPJ = /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/;
    let regexCEP = /^\d{5}-\d{3}$/;
    let regexTel = /^\(\d{2}\) \d{4,5}-\d{4}$/;

    if (!regexCNPJ.test(cnpj)) {
        alert("CNPJ inválido! Use o formato 00.000.000/0000-00");
        document.getElementById('cnpj').focus();
        return;
    }
    
    if (!regexCEP.test(cep)) {
        alert("CEP inválido! Use o formato 00000-000");
        document.getElementById('cep').focus();
        return;
    }
    
    if (!regexTel.test(contato)) {
        alert("Número inválido! Use o formato (00) 00000-0000");
        document.getElementById('numero').focus();
        return;
    }


    try {
        let cepSemMascara = cep.replace(/\D/g, "");
        console.log('Consultando CEP:', cepSemMascara);
        
        let response = await fetch(`https://viacep.com.br/ws/${cepSemMascara}/json/`);
        let data = await response.json();
        
        if (data.erro) {
            alert("CEP não encontrado! Verifique o CEP informado.");
            return;
        }
        

        if (!endereco) {
            endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
            document.getElementById('endereco').value = endereco;
        }
    } catch (erro) {
        console.log('Erro ao consultar CEP:', erro);

    }

    const formData = new FormData();
    formData.append('cnpj', cnpj);
    formData.append('razao', razaoSocial);
    formData.append('cep', cep);
    formData.append('endereco', endereco);
    formData.append('contato', contato);
    formData.append('descricao', descricao);
    formData.append('acao', indiceEditando === null ? 'adicionar' : 'editar');
    
    if (indiceEditando !== null) {
        formData.append('cnpjOriginal', fornecedores[indiceEditando].cnpj);
    }

    console.log('Enviando dados para o servidor...');

    try {
        const resposta = await fetch('gerenciar_fornecedores.php', {
            method: 'POST',
            body: formData
        });

        if (!resposta.ok) {
            throw new Error('Erro HTTP: ' + resposta.status);
        }

        const resultado = await resposta.json();
        console.log('Resposta do servidor:', resultado);

        if (resultado.status === 'erro') {
            alert('Erro: ' + resultado.msg);
            return;
        }


        await carregarFornecedores();
        limparCampos();
        
        alert(indiceEditando === null ? 'Fornecedor adicionado com sucesso!' : 'Fornecedor atualizado com sucesso!');
        
    } catch (erro) {
        console.error('Erro detalhado:', erro);
        alert("Erro ao salvar fornecedor no servidor! Verifique o console para detalhes.");
    }
}


function atualizarTabela() {
    const corpo = document.getElementById("corpoTabela");
    corpo.innerHTML = '';

    if (fornecedores.length === 0) {
        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = 7;
        td.textContent = 'Nenhum fornecedor cadastrado';
        td.style.textAlign = 'center';
        td.style.padding = '20px';
        td.style.color = '#666';
        tr.appendChild(td);
        corpo.appendChild(tr);
        return;
    }

    fornecedores.forEach((fornecedor, index) => {
        const tr = document.createElement('tr');


        const dados = [
            fornecedor.cnpj || '',
            fornecedor.razaoSocial || '',
            fornecedor.cep || '',
            fornecedor.endereco || '',
            fornecedor.contato || '',
            fornecedor.descricao || ''
        ];

        dados.forEach(valor => {
            const td = document.createElement('td');
            td.textContent = valor;
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
        btnEditar.onclick = () => editarFornecedor(index);

        const btnRemover = document.createElement('button');
        btnRemover.textContent = 'Remover';
        btnRemover.className = 'btn-remover';
        btnRemover.onclick = () => removerFornecedor(index);

        tdAcoes.appendChild(btnEditar);
        tdAcoes.appendChild(btnRemover);
        tr.appendChild(tdAcoes);

        corpo.appendChild(tr);
    });
}


function editarFornecedor(index) {
    const fornecedor = fornecedores[index];
    console.log('Editando fornecedor:', fornecedor);
    
    document.getElementById("cnpj").value = fornecedor.cnpj || '';
    document.getElementById("nome").value = fornecedor.razaoSocial || '';
    document.getElementById("cep").value = fornecedor.cep || '';
    document.getElementById("endereco").value = fornecedor.endereco || '';
    document.getElementById("numero").value = fornecedor.contato || '';
    document.getElementById("descricao").value = fornecedor.descricao || '';

    indiceEditando = index;
    document.getElementById("BtnAdicionar").textContent = "Salvar Edição";
    
    document.querySelector('.formulario_produto').scrollIntoView({ behavior: 'smooth' });
}


async function removerFornecedor(index) {
    const fornecedor = fornecedores[index];
    
    if (!confirm(`Tem certeza que deseja remover o fornecedor "${fornecedor.razaoSocial}"?`)) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('acao', 'remover');
        formData.append('cnpj', fornecedor.cnpj);

        const resposta = await fetch('gerenciar_fornecedores.php', {
            method: 'POST',
            body: formData
        });

        const resultado = await resposta.json();

        if (resultado.status === 'sucesso') {
            await carregarFornecedores();
            alert('Fornecedor removido com sucesso!');
        } else {
            alert('Erro ao remover fornecedor: ' + resultado.msg);
        }
    } catch (erro) {
        console.error('Erro ao remover:', erro);
        alert('Erro ao remover fornecedor do servidor!');
    }
}


function limparCampos() {
    document.getElementById("cnpj").value = "";
    document.getElementById("nome").value = "";
    document.getElementById("cep").value = "";
    document.getElementById("endereco").value = "";
    document.getElementById("numero").value = "";
    document.getElementById("descricao").value = "";
    
    indiceEditando = null;
    document.getElementById("BtnAdicionar").textContent = "Adicionar";
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