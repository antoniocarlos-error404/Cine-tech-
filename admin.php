<?php include('views/header.php'); ?>

<h1 class="text-center mb-4">🎬 Área Administrativa</h1>

<div class="card shadow p-4">
    <form id="form-filme" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
        </div>
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" required></textarea>
        </div>
        <div class="mb-3">
            <label for="genero" class="form-label">Gênero</label>
            <select class="form-control" id="genero" name="genero_id" required>
                <option value="">Selecione um gênero</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem</label>
            <input type="file" class="form-control" id="imagem" name="imagem" required>
        </div>
        <div class="mb-3">
            <label for="trailer" class="form-label">Link do Trailer</label>
            <input type="url" class="form-control" id="trailer" name="trailer" required placeholder="https://example.com/trailer">
        </div>
        <button type="submit" class="btn btn-success w-100">Salvar</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Carregar gêneros dinamicamente
        fetch('api.php?tipo=genero')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar gêneros');
                }
                return response.json();
            })
            .then(data => {
                let options = '<option value="">Selecione um gênero</option>';
                data.forEach(genero => {
                    options += `<option value="${genero.id}">${genero.nome}</option>`;
                });
                document.getElementById('genero').innerHTML = options;
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar os gêneros.');
            });

        // Salvar filme
        document.getElementById('form-filme').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao salvar o filme');
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
                window.location.reload();  // Atualiza a página após salvar
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar o filme.');
            });
        });
    });
</script>

<?php include('views/footer.php'); ?>
