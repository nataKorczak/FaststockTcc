class TemaGlobal {
    constructor() {
        this.temaAtual = localStorage.getItem('faststock-tema') || 'padrao';
        this.aplicarTemaGlobal();
    }

    aplicarTemaGlobal() {
        
        document.body.classList.remove('tema-padrao', 'tema-escuro');
        
        
        document.body.classList.add('tema-' + this.temaAtual);
        
        console.log('Tema aplicado:', this.temaAtual);
    }

    mudarTema(novoTema) {
        this.temaAtual = novoTema;
        localStorage.setItem('faststock-tema', novoTema);
        this.aplicarTemaGlobal();
        
        
        window.dispatchEvent(new CustomEvent('temaAlterado', { detail: novoTema }));
    }

    getTemaAtual() {
        return this.temaAtual;
    }
}


document.addEventListener('DOMContentLoaded', function() {
    window.temaGlobal = new TemaGlobal();
});


function mudarTemaGlobal(tema) {
    if (window.temaGlobal) {
        window.temaGlobal.mudarTema(tema);
    }
}

function getTemaGlobal() {
    return window.temaGlobal ? window.temaGlobal.getTemaAtual() : 'padrao';
}