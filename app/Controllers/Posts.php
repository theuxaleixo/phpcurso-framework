<?php
/* 
Controle de Postagens
Controlador responsavel pelo controle de dados e comunicação com o model de postagens
 */
class Posts extends Controller
{

    public function __construct()
    {
        //checa se o usuário não está logado redirecionando para página de login
        if (!Sessao::estaLogado()) :
            Url::redirecionar('usuarios/login');
        endif;

        //chama os modelos responsáveis por fazer a comunicação com o banco de dados
        $this->postModel = $this->model('Post');
        $this->usuarioModel = $this->model('Usuario');
    }

    /* exibe as postagens na index */
    public function index()
    {
        //exibe uma mensagem caso não tenha nenhum post cadastrado
        if ($this->postModel->lerPosts() == null) :
            Sessao::mensagem('post', 'Nenhum post cadastrado para exibir.', 'alert alert-info');
        endif;
        
        //define os dados dos posts
        $dados = [
            'posts' => $this->postModel->lerPosts()
        ];
        //define a view para exibir os posts
        $this->view('posts/index', $dados);
    }

    /* checa e cadastra posts */
    public function cadastrar()
    {

        //recebe os dados do formulario e os filtra
        $formulario = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($formulario)) :
            $dados = [
                'titulo' => trim($formulario['titulo']),
                'texto' => trim($formulario['texto']),
                'usuario_id' => $_SESSION['usuario_id']
            ];

            //checa campos em branco
            if (in_array("", $formulario)) :

                if (empty($formulario['titulo'])) :
                    $dados['titulo_erro'] = 'Preencha o campo titulo';
                endif;

                if (empty($formulario['texto'])) :
                    $dados['texto_erro'] = 'Preencha o campo texto';
                endif;

            else :
                //chama o metodo armazenar do modelo Post para cadastrar os dados no banco de dados
                if ($this->postModel->armazenar($dados)) :
                    Sessao::mensagem('post', 'Post cadastrado com sucesso');
                    Url::redirecionar('posts');
                else :
                    die("Erro ao armazenar post no banco de dados");
                endif;

            endif;
        else :
            $dados = [
                'titulo' => '',
                'texto' => '',
                'titulo_erro' => '',
                'texto_erro' => ''
            ];

        endif;
        //define a view do formulario de cadastro de posts
        $this->view('posts/cadastrar', $dados);
    }

    /* checa e edita os dados do post por seu ID */
    public function editar($id)
    {

        $formulario = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($formulario)) :
            $dados = [
                'id' => $id,
                'titulo' => trim($formulario['titulo']),
                'texto' => trim($formulario['texto'])
            ];

            if (in_array("", $formulario)) :

                if (empty($formulario['titulo'])) :
                    $dados['titulo_erro'] = 'Preencha o campo titulo';
                endif;

                if (empty($formulario['texto'])) :
                    $dados['texto_erro'] = 'Preencha o campo texto';
                endif;

            else :
                if ($this->postModel->atualizar($dados)) :
                    Sessao::mensagem('post', 'Post atualizado com sucesso');
                    Url::redirecionar('posts');
                else :
                    die("Erro ao atualizar o post");
                endif;

            endif;
        else :

            $post = $this->postModel->lerPostPorId($id);

            if ($post->usuario_id != $_SESSION['usuario_id']) :
                Sessao::mensagem('post', 'Você não tem autorização para editar esse Post', 'alert alert-danger');
                Url::redirecionar('posts');
            endif;

            $dados = [
                'id' => $post->id,
                'titulo' => $post->titulo,
                'texto' => $post->texto,
                'titulo_erro' => '',
                'texto_erro' => ''
            ];

        endif;

        $this->view('posts/editar', $dados);
    }


    /* exibe o post com os dados do escritor  */
    public function ver($id)
    {
        //chama o metodo para ler posts por seu ID no modelo Post
        $post = $this->postModel->lerPostPorId($id);

        if($post == null){
            Url::redirecionar('paginas/error');
        }

        //chama o metodo para ler o usuario por seu ID no modelo Usuario
        $usuario = $this->usuarioModel->lerUsuarioPorId($post->usuario_id);

        //define os dados da view
        $dados = [
            'post' => $post,
            'usuario' => $usuario
        ];
        //define a view para ver o post
        $this->view('posts/ver', $dados);
    }

    public function deletar($id)
    {
        //chama a função para checar se o usuário tem autorização para deletar o post
        if (!$this->checarAutorizacao($id)) :
            //filter_var - Filtra a variável com um especificado filtro
            //FILTER_VALIDATE_INT - Verifica se a variável é um número inteiro
            $id = filter_var($id, FILTER_VALIDATE_INT);
            /* filter_input — Obtem a específica variável externa pelo nome e opcionalmente a filtra
            INPUT_SERVER - Constante pré-definida
            REQUEST_METHOD - Contém o método de request utilizando para acessar a página. Geralmente 'GET', 'HEAD', 'POST' ou 'PUT'
            FILTER_SANITIZE_STRING - Remova todas as tags HTML de uma string
             */
            $metodo = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
            //checa se o ID retorna true e se o metódo é igual a POST
            if ($id && $metodo == 'POST') :
                //chama o metodo destruir do modelo Post por seu ID
                if ($this->postModel->destruir($id)) :
                    Sessao::mensagem('post', 'Post deletado com sucesso!');
                    Url::redirecionar('posts');
                endif;
            else :
                Sessao::mensagem('post', 'Você não tem autorização para deletar esse Post', 'alert alert-danger');
                Url::redirecionar('posts');
            endif;

        else :
            Sessao::mensagem('post', 'Você não tem autorização para deletar esse Post', 'alert alert-danger');
            Url::redirecionar('posts');
        endif;
    }

    /* checa se o usuario que escreveu o post é o mesmo que está logado */
    private function checarAutorizacao($id)
    {
        $post = $this->postModel->lerPostPorId($id);
        if ($post->usuario_id != $_SESSION['usuario_id']) :
            return true;
        else :
            return false;
        endif;
    }
}
