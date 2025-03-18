<?php include('views/header.php'); ?>

<h1 class="text-center mb-4">🎬 Lista de Filmes</h1>

<div class="row" id="lista-filmes">
    <!-- Os filmes serão carregados dinamicamente aqui -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetch('api.php?tipo=filme')  // Certifique-se de que o caminho para o arquivo da API esteja correto.
            .then(response => response.json())
            .then(data => {
                let html = '';
                data.forEach(filme => {
                    html += `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <img src="${filme.capa}" class="card-img-top" alt="${filme.titulo}">
                                <div class="card-body">
                                    <h5 class="card-title">${filme.titulo}</h5>
                                    <p class="card-text">${filme.sinopse}</p>
                                    <a href="${filme.trailer}" target="_blank" class="btn btn-primary w-100">Assistir Trailer</a>
                                    <button class="btn btn-secondary w-100 mt-2" onclick="saveLink('${filme.trailer}')">Salvar Link</button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                document.getElementById('lista-filmes').innerHTML = html;
            })
            .catch(error => console.error('Erro ao carregar filmes:', error));
    });

    // Função para salvar o link do trailer no localStorage
    function saveLink(link) {
        // Verifica se já existe um array de links salvo no localStorage
        let savedLinks = JSON.parse(localStorage.getItem('savedLinks')) || [];
        
        // Adiciona o novo link à lista
        savedLinks.push(link);

        // Salva a lista atualizada de links
        localStorage.setItem('savedLinks', JSON.stringify(savedLinks));

        alert('Link salvo com sucesso!');
    }
</script>

<?php include('views/footer.php'); ?>
