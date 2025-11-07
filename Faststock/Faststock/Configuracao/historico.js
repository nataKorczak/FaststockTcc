
class GerenciadorHistorico {
    constructor() {
        this.dados = [];
        this.filtros = {
            tipo: '',
            categoria: '',
            dataInicio: '',
            dataFim: ''
        };
        this.init();
    }

    init() {
        console.log('Iniciando Gerenciador de Histórico...');
        this.configurarEventos();
    }

    configurarEventos() {

        document.querySelectorAll('.filtros select, .filtros input').forEach(element => {
            element.addEventListener('change', () => this.aplicarFiltros());
        });
    }


    aplicarFiltros() {
        const tipo = document.getElementById('filtroTipo').value;
        const categoria = document.getElementById('filtroCategoria').value;
        const dataInicio = document.getElementById('filtroDataInicio').value;
        const dataFim = document.getElementById('filtroDataFim').value;

        this.filtros = { tipo, categoria, dataInicio, dataFim };
        

        this.mostrarLoading();
        

        setTimeout(() => {
            this.filtrarDados();
            this.esconderLoading();
        }, 500);
    }

    mostrarLoading() {

        console.log('Aplicando filtros...');
    }

    esconderLoading() {
        console.log('Filtros aplicados!');
    }

    filtrarDados() {

        console.log('Filtrando dados com:', this.filtros);
    }

    exportarParaCSV() {

        console.log('Exportando histórico para CSV...');
    }


    formatarData(data) {
        return new Date(data).toLocaleDateString('pt-BR');
    }

    formatarHora(data) {
        return new Date(data).toLocaleTimeString('pt-BR');
    }
}


document.addEventListener('DOMContentLoaded', function() {
    window.gerenciadorHistorico = new GerenciadorHistorico();
});


function aplicarFiltros() {
    if (window.gerenciadorHistorico) {
        window.gerenciadorHistorico.aplicarFiltros();
    }
}

function limparFiltros() {
    document.getElementById('filtroTipo').value = '';
    document.getElementById('filtroCategoria').value = '';
    document.getElementById('filtroDataInicio').value = '';
    document.getElementById('filtroDataFim').value = '';
    
    if (window.gerenciadorHistorico) {
        window.gerenciadorHistorico.aplicarFiltros();
    }
}

function exportarHistorico() {
    if (window.gerenciadorHistorico) {
        window.gerenciadorHistorico.exportarParaCSV();
    }
}