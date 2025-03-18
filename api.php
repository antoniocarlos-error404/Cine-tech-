<?php
// Inclua a conexão com o banco de dados
include('config/db.php');

// Verifica se o tipo de requisição é para filmes
if (isset($_GET['tipo']) && $_GET['tipo'] == 'filme') {
    // Consulta SQL para buscar filmes
    $query = "SELECT f.*, g.nome AS genero_nome FROM filmes f
              LEFT JOIN generos g ON f.genero_id = g.id";
    $result = $db->query($query);

    $filmes = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $filmes[] = $row;
    }

    // Retorna os filmes como JSON
    echo json_encode($filmes);
}
?>
