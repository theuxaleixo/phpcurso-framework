<?php
/* Post Model - Classe responsavel pelos posts e a comunicação com o banco de dados */
class Post
{
    //atributo privado para manipular o banco de dados
    private $db;

    public function __construct()
    {
        //instancia a classe de conexão com o banco de dados
        $this->db = new Database();
    }

    public function lerPosts(){
        //consulta com associações de tabelas
        //INNER JOIN permite usar um operador de comparação para comparar os valores de colunas provenientes de tabelas associadas.
        $this->db->query("SELECT *,
        posts.id as postId,
        posts.criado_em as postDataCadastro,
        usuarios.id as usuarioId,
        usuarios.criado_em as usuarioDataCadastro
         FROM posts
         INNER JOIN usuarios ON
         posts.usuario_id = usuarios.id
         ORDER BY posts.id DESC
         ");
        return $this->db->resultados();
    }

    //armazena o post no banco de dados
    public function armazenar($dados)
    {
        $this->db->query("INSERT INTO posts(usuario_id, titulo, texto) VALUES (:usuario_id, :titulo, :texto)");

        $this->db->bind("usuario_id", $dados['usuario_id']);
        $this->db->bind("titulo", $dados['titulo']);
        $this->db->bind("texto", $dados['texto']);

        if ($this->db->executa()) :
            return true;
        else :
            return false;
        endif;
    }

    //atualiza o post no banco de dados
    public function atualizar($dados)
    {
        $this->db->query("UPDATE posts SET titulo = :titulo, texto = :texto WHERE id = :id");

        $this->db->bind("id", $dados['id']);
        $this->db->bind("titulo", $dados['titulo']);
        $this->db->bind("texto", $dados['texto']);

        if ($this->db->executa()) :
            return true;
        else :
            return false;
        endif;
    }

    //le post no banco de dados por seu ID
    public function lerPostPorId($id){
        $this->db->query("SELECT * FROM posts WHERE id = :id");
        $this->db->bind('id', $id);

        return $this->db->resultado();
    }

    //deleta o post no banco de dados por seu ID
    public function destruir($id)
    {
        $this->db->query("DELETE FROM posts  WHERE id = :id");
        $this->db->bind("id", $id);

        if ($this->db->executa()) :
            return true;
        else :
            return false;
        endif;
    }


}
