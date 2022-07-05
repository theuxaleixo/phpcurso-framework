<?php
/* 
Controle de Usuários
Controlador responsavel pelo controle de dados e comunicação com o model de usuarios
 */
class Usuarios extends Controller
{

    public function __construct()
    {
        //$this = Pseudo-variável é um nome que será utilizado como se fosse uma variável, para chamar o modelo de Usuarios que realiza a comunicação com o banco de dados
        $this->usuarioModel = $this->model('Usuario');
    }

    /* checa e edita os dados do usuário por seu ID */
    public function perfil($id)
    {
        //busca o usuario no model pelo seu ID
        $usuario = $this->usuarioModel->lerUsuarioPorId($id);

        //recebe os dados do formulario e os filtra
        $formulario = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($formulario)) :
            //define os dados
            $dados = [
                'id' => $id,
                'nome' => trim($formulario['nome']),
                'email' => trim($formulario['email']),
                'senha' => trim($formulario['senha']),
                'biografia' => trim($formulario['biografia']),
            ];
            //checa se o campo de senha está vazio 
            if (empty($formulario['senha'])) :
                //define a senha como a senha do usuario no banco de dados
                $dados['senha'] = $usuario->senha;
            else :
                //se o campo de senha não estiver vazio codifica a senha
                $dados['senha'] = password_hash($formulario['senha'], PASSWORD_DEFAULT);
            endif;

            //se a biografia estivar vazia recebe a mesma biografia do banco
            if (empty($formulario['biografia'])) :
                $dados['biografia'] = $usuario->biografia;
            endif;

            //checa se existe campos em branco
            if (in_array("", $dados)) :

                if (empty($formulario['nome'])) :
                    $dados['nome_erro'] = 'Preencha o campo nome';
                endif;

                if (empty($formulario['email'])) :
                    $dados['email_erro'] = 'Preencha o campo e-mail';
                endif;

            else :
                //checa se o email do formulario é igual do usuario no banco de dados
                if ($formulario['email'] == $usuario->email) :
                    $this->usuarioModel->atualizar($dados);
                    Sessao::mensagem('usuario', 'Perfil atualizado com sucesso');
                //checa se o e-mail não está cadastrado no banco de dados
                elseif (!$this->usuarioModel->checarEmail($formulario['email'])) :
                    $this->usuarioModel->atualizar($dados);
                    Sessao::mensagem('usuario', 'Perfil atualizado com sucesso');
                else :
                    $dados['email_erro'] = 'O e-mail informado já está cadastrado';
                endif;

            endif;
        else :
            //verifica se o usuario tem autorização para editar seu perfil
            if ($usuario->id != $_SESSION['usuario_id']) :
                Sessao::mensagem('post', 'Você não tem autorização para editar esse perfil', 'alert alert-danger');
                Url::redirecionar('posts');
            endif;

            //define os dados da view
            $dados = [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'biografia' => $usuario->biografia,
                'nome_erro' => '',
                'email_erro' => '',
                'senha_erro' => ''
            ];

        endif;
        //define o arquivo de view 
        $this->view('usuarios/perfil', $dados);
    }

    /* checa e cadastra usuarios */
    public function cadastrar()
    {
        //recebe os dados do formulario e os filtra
        $formulario = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        //define os dados padrão da view
        $dados = [
            'nome' => '',
            'email' => '',
            'senha' => '',
            'confirma_senha' => '',
            'nome_erro' => '',
            'email_erro' => '',
            'senha_erro' => '',
            'confirma_senha_erro' => ''
        ];
        if (isset($formulario)) :
            $dados['nome'] = trim($formulario['nome']);
            $dados['email'] = trim($formulario['email']);
            $dados['senha'] = trim($formulario['senha']);
            $dados['confirma_senha'] = trim($formulario['confirma_senha']);

            //checa campos em brancos
            if (in_array("", $formulario)) :

                if (empty($formulario['nome'])) :
                    $dados['nome_erro'] = 'Preencha o campo nome';
                endif;

                if (empty($formulario['email'])) :
                    $dados['email_erro'] = 'Preencha o campo e-mail';
                endif;

                if (empty($formulario['senha'])) :
                    $dados['senha_erro'] = 'Preencha o campo senha';
                endif;

                if (empty($formulario['confirma_senha'])) :
                    $dados['confirma_senha_erro'] = 'Confirme a Senha';
                endif;
            else :
                //checa se o nome tem um formato valido
                if (Checa::checarNome($formulario['nome'])) :
                    $dados['nome_erro'] = 'O nome informado é invalido';
                //checa se o e-mail tem um formato valido
                elseif (Checa::checarEmail($formulario['email'])) :
                    $dados['email_erro'] = 'O e-mail informado é invalido';
                //checa se o e-mail existe no banco de dados
                elseif ($this->usuarioModel->checarEmail($formulario['email'])) :
                    $dados['email_erro'] = 'O e-mail informado já está cadastrado';
                //checa se a senha tem menos de 6 caracteres
                elseif (strlen($formulario['senha']) < 6) :
                    $dados['senha_erro'] = 'A senha deve ter no minimo 6 caracteres';
                //checa se a senha é igual a confirmação de senha
                elseif ($formulario['senha'] != $formulario['confirma_senha']) :
                    $dados['confirma_senha_erro'] = 'As senhas são diferentes';
                else :
                    /* 
                    Codifica a senha
                    password_hash() cria um novo password hash usando um algoritmo forte de hash de via única. PASSWORD_DEFAULT - Usa o algoritmo bcrypt (padrão desde o PHP 5.5.0).
                     */
                    $dados['senha'] = password_hash($formulario['senha'], PASSWORD_DEFAULT);
                    //chama o metodo armazenar do model para cadastrar os dados no banco de dados
                    if ($this->usuarioModel->armazenar($dados)) :
                        Sessao::mensagem('usuario', 'Cadastro realizado com sucesso');
                        Url::redirecionar('usuarios/login');
                    else :
                        die("Erro ao armazenar usuario no banco de dados");
                    endif;

                endif;

            endif;


        endif;


        //define a view de cadastro de usuarios
        $this->view('usuarios/cadastrar', $dados);
    }


    /* checa e realiza login do usuario */
    public function login()
    {
        //recebe os dados do formulario e os filtra
        $formulario = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($formulario)) :
            $dados = [
                'email' => trim($formulario['email']),
                'senha' => trim($formulario['senha']),
            ];

            //checa campos em branco
            if (in_array("", $formulario)) :

                if (empty($formulario['email'])) :
                    $dados['email_erro'] = 'Preencha o campo e-mail';
                endif;

                if (empty($formulario['senha'])) :
                    $dados['senha_erro'] = 'Preencha o campo senha';
                endif;

            else :
                //checa se o e-mail informado é válido
                if (Checa::checarEmail($formulario['email'])) :
                    $dados['email_erro'] = 'O e-mail informado é invalido';
                else :
                    //checa os dados de login no banco de dados
                    $usuario = $this->usuarioModel->checarLogin($formulario['email'], $formulario['senha']);
                    //se o usuario retornar true
                    if ($usuario) :
                        //chama o metódo para criar a sessão do usuário
                        $this->criarSessaoUsuario($usuario);
                    else :
                        //mensagem de usuario ou senha incorretos
                        Sessao::mensagem('usuario', 'Usuario ou senha invalidos', 'alert alert-danger');
                    endif;
                endif;
            endif;
        else :
            //define os dados em branco na view
            $dados = [
                'email' => '',
                'senha' => '',
                'email_erro' => '',
                'senha_erro' => ''
            ];
        endif;
        //define a view de login
        $this->view('usuarios/login', $dados);
    }

    /* 
    cria sessão com informações do usuário
    */
    private function criarSessaoUsuario($usuario)
    {
        /* Sessões são uma forma simples de armazenar dados para usuários individuais usando um ID de sessão único. Sessões podem ser usadas para persistir informações entre requisições de páginas. */

        //definir variáveis ​​de sessão
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;
        $_SESSION['usuario_email'] = $usuario->email;
        //redireciona para a pagina posts apos criar a sessão do usuário
        Url::redirecionar('posts');
    }

    /* Faz o logout do usuário */
    public function sair()
    {
        //unset — Destrói a variável especificada
        unset($_SESSION['usuario_id']);
        unset($_SESSION['usuario_nome']);
        unset($_SESSION['usuario_email']);
        //session_destroy — Destrói todos os dados registrados em uma sessão
        session_destroy();
        //redireciona para a pagina de login apos sair do sistema
        Url::redirecionar('usuarios/login');
    }
}
