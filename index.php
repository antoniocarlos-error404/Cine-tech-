<?php include('views/header.php'); ?>

<h1 class="text-center mb-4">🎬 Lista de Filmes</h1>

<div class="row mb-5 d-flex justify-content-center">
    <div class="col-md-5 mb-3 mb-md-0">
        <!-- Campo de pesquisa -->
        <input type="text" id="pesquisa" class="form-control" placeholder="🔎 Buscar filme..." oninput="filtrarFilmes()">
    </div>
    <div class="col-md-5">
        <!-- Filtro por gênero (Dropdown Responsivo) -->
        <div class="dropdown w-100">
            <button class="btn dropdown-toggle w-100" type="button" id="dropdownGenero" 
                data-bs-toggle="dropdown" aria-expanded="false" 
                style="background-color: #333; color: white; border: 1px solid #444;">
                Todos os Gêneros
            </button>
            <ul class="dropdown-menu w-100" id="filtro-genero" 
                style="background-color: #222; border: 1px solid #444;">
                <li><a class="dropdown-item" href="#" data-value="" onclick="selecionarGenero(this)" style="color: white;">Todos os Gêneros</a></li>
            </ul>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/styles.css">

<div class="row justify-content-center" id="lista-filmes">
    <!-- Os filmes serão carregados dinamicamente aqui -->
</div>

<!-- Modal para detalhes do filme -->
<div class="modal fade" id="detalhesFilmeModal" tabindex="-1" aria-labelledby="detalhesFilmeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalhesFilmeModalLabel">Detalhes do Filme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <img id="detalhesCapa" src="" class="img-fluid mb-3" alt="Imagem do Filme">
                <h5 id="detalhesTitulo"></h5>
                <p id="detalhesSinopse"></p>
                <p id="duracao"></p>
                <p id="data_lancamento"></p>  
                <a id="detalhesTrailer" href="#" target="_blank" class="btn btn-primary w-100">Assistir Trailer</a>
            </div>
        </div>
    </div>
</div>

<script>
    let filmes = [];

    document.addEventListener('DOMContentLoaded', () => {
        carregarFilmes();
        carregarGeneros();
    });

    function carregarFilmes() {
        fetch('api.php?tipo=filme')
            .then(response => response.json())
            .then(data => {
                filmes = data;
                renderizarFilmes(filmes);
            })
            .catch(error => console.error('Erro ao carregar filmes:', error));
    }

    function renderizarFilmes(lista) {
        let html = '';
        lista.forEach(filme => {
            html += `
                <div class="col-md-4 mb-4 d-flex justify-content-center" data-id="${filme.id}">
                    <div class="card h-100 shadow-sm" style="width: 30rem;">
                        <img src="${filme.capa}" class="card-img-top" alt="${filme.titulo}">
                        <div class="card-body">
                            <h5 class="card-title">${filme.titulo}</h5>
                            <p class="card-text">${filme.sinopse}</p>
                            <a href="${filme.link}" target="_blank" class="btn btn-primary w-100">Assistir Trailer</a>
                            <button class="btn btn-info w-100 mt-2" onclick="verDetalhes('${filme.titulo}', '${filme.sinopse}', '${filme.capa}', '${filme.link}', '${filme.duracao}', '${filme.data_lancamento}')">Ver Detalhes</button>
                            <button class="btn btn-danger w-100 mt-2" onclick="excluirFilme(${filme.id})">Excluir</button>
                        </div>
                    </div>
                </div>
            `;
        });
        document.getElementById('lista-filmes').innerHTML = html;
    }

    function excluirFilme(id) {
        if (!confirm('Tem certeza que deseja excluir este filme?')) return;
        
        const botao = document.querySelector(`div[data-id="${id}"] .btn-danger`);
        botao.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Excluindo...';
        botao.disabled = true;

        fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&_method=DELETE&tipo=filme`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert(`Erro ao excluir filme: ${data.message}`);
                botao.innerHTML = 'Excluir';
                botao.disabled = false;
            }
        })
        .catch(error => {
            console.error('Erro ao excluir filme:', error);
            alert('Erro ao excluir o filme.');
            botao.innerHTML = 'Excluir';
            botao.disabled = false;
        });
    }

    function carregarGeneros() {
        fetch('api.php?tipo=genero')
            .then(response => response.json())
            .then(data => {
                const filtroGenero = document.getElementById('filtro-genero');

                data.forEach(genero => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item" href="#" data-value="${genero.nome.toLowerCase()}" onclick="selecionarGenero(this)" style="color: white;">${genero.nome}</a>`;
                    filtroGenero.appendChild(li);
                });
            })
            .catch(error => console.error('Erro ao carregar gêneros:', error));
    }

    function selecionarGenero(elemento) {
        const botao = document.getElementById('dropdownGenero');
        const valor = elemento.getAttribute('data-value');
        const nome = elemento.innerText;

        botao.innerText = nome; // Atualiza o texto do botão
        botao.setAttribute('data-value', valor); // Salva o valor do gênero

        filtrarFilmes(); // Aciona a filtragem
    }

    function filtrarFilmes() {
        if (filmes.length === 0) {
            console.warn('Nenhum filme carregado ainda.');
            return;
        }

        const termoPesquisa = document.getElementById('pesquisa').value.toLowerCase();
        const generoSelecionado = document.getElementById('dropdownGenero').getAttribute('data-value');

        const filmesFiltrados = filmes.filter(filme => {
            const correspondeNome = filme.titulo.toLowerCase().includes(termoPesquisa);
            const correspondeGenero = !generoSelecionado || filme.genero.toLowerCase() === generoSelecionado;
            return correspondeNome && correspondeGenero;
        });

        renderizarFilmes(filmesFiltrados);
    }

    function verDetalhes(titulo, sinopse, capa, trailer, duracao, data_lancamento) {
        document.getElementById('detalhesTitulo').innerText = titulo;
        document.getElementById('detalhesSinopse').innerText = sinopse;
        document.getElementById('detalhesCapa').src = capa;
        document.getElementById('detalhesTrailer').href = trailer;
        document.getElementById('duracao').innerText = `Duração: ${duracao} horas`;
        document.getElementById('data_lancamento').innerText = `Lançamento: ${data_lancamento}`;

        const modal = new bootstrap.Modal(document.getElementById('detalhesFilmeModal'));
        modal.show();
    }
</script>

<?php include('views/footer.php'); ?>
