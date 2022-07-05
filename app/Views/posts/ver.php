<div class="container">
    <div class="p-5 m-5 bg-light rounded border shadow">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= URL ?>/posts" data-toggle="tooltip" title="Postagens">Posts</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $dados['post']->titulo ?></li>
            </ol>
        </nav>

        <div class="card text-center">
            <div class="card-header bg-secondary text-white font-weight-bold">
                <?= $dados['post']->titulo ?>
            </div>
            <div class="card-body">
                <p class="card-text"><?= $dados['post']->texto ?></p>
            </div>
            <div class="card-footer text-muted">
                <small>
                    Escrito por: <b><?= $dados['usuario']->nome ?></b> em <i><?= Checa::dataBr($dados['post']->criado_em) ?></i>
                </small>
            </div>

            <?php if ($dados['post']->usuario_id == $_SESSION['usuario_id']) : ?>
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a href="<?= URL . '/posts/editar/' . $dados['post']->id ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Editar o Post  <?= $dados['post']->titulo ?>">Editar</a>
                    </li>
                    <li class="list-inline-item">
                        <form action="<?= URL . '/posts/deletar/' . $dados['post']->id ?>" method="POST">
                            <input type="submit" class="btn btn-sm btn-danger" value="Deletar" data-toggle="tooltip" title="Deletar o Post  <?= $dados['post']->titulo ?>">
                        </form>
                    </li>
                </ul>

            <?php endif ?>
        </div>
    </div>
</div>