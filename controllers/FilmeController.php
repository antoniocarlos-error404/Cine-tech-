<?php

class FilmeController {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Método para cadastrar filme
    public function cadastrarFilme($titulo, $sinopse, $generos, $capa, $trailer, $data_lancamento, $duracao) {
        // Salvar imagem na pasta 'uploads/imagens'
        $capa_nome = time() . '-' . basename($capa['name']);
        $capa_destino = 'uploads/imagens/' . $capa_nome;

        // Move a imagem para a pasta
        if (move_uploaded_file($capa['tmp_name'], $capa_destino)) {
            // Insere os dados do filme no banco de dados
            $query = $this->db->prepare("INSERT INTO filmes (titulo, sinopse, capa, trailer, data_lancamento, duracao) VALUES (?, ?, ?, ?, ?, ?)");
            $query->execute([$titulo, $sinopse, $capa_destino, $trailer, $data_lancamento, $duracao]);

            $filme_id = $this->db->lastInsertId();

            // Relaciona o filme aos gêneros
            foreach ($generos as $genero_id) {
                $queryGenero = $this->db->prepare("INSERT INTO filme_genero (filme_id, genero_id) VALUES (?, ?)");
                $queryGenero->execute([$filme_id, $genero_id]);
            }

            return true;
        }
        return false;
    }
}
?>
