<?php
// Inclua a conexão com o banco de dados
include('config/db.php');

// Habilitar CORS para permitir requisições de qualquer origem
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Buscar filmes
if (isset($_GET['tipo']) && $_GET['tipo'] == 'filme') {
    $query = "SELECT f.*, g.nome AS genero FROM filmes f
              LEFT JOIN generos g ON f.genero_id = g.id";
    $result = $db->query($query);

    $filmes = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        // Corrigir o caminho da imagem para garantir que esteja correto
        $row['capa'] = !empty($row['capa']) ? 'imagens/' . $row['capa'] : 'imagens/default.png';
        $filmes[] = $row;
    }

    echo json_encode($filmes);
    exit;
}

// Buscar gêneros
if (isset($_GET['tipo']) && $_GET['tipo'] == 'genero') {
    $query = "SELECT id, nome FROM generos";
    $result = $db->query($query);

    $generos = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $generos[] = $row;
    }

    echo json_encode($generos);
    exit;
}

// Excluir filme
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($id) {
        // Primeiro, verifica e exclui a imagem associada ao filme
        $stmt = $db->prepare("SELECT capa FROM filmes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $filme = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o filme tiver uma imagem associada, exclui a imagem
        if ($filme && file_exists('imagens/' . $filme['capa'])) {
            unlink('imagens/' . $filme['/imagens/']);
        }

        // Exclui o filme da tabela
        $stmt = $db->prepare("DELETE FROM filmes WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Filme excluído com sucesso!']);
        } else {
            echo json_encode(['message' => 'Erro ao excluir o filme.']);
        }
    } else {
        echo json_encode(['message' => 'ID inválido para exclusão.']);
    }

    exit;
};
