<div class="container py-5">
    <?= Sessao::mensagem('post') ?>
    <div class="card bg-light">
        <div class="card-header bg-info text-white">
            POSTAGENS
            <div class="float-right">
                <a href="<?= URL ?>/posts/cadastrar" class="btn btn-light" data-toggle="tooltip" title="Escrever um Post">Escrever</a>
            </div>
        </div>
        <div class="card-body">
            <?php foreach ($dados['posts'] as $post) : ?>
                <div class="card m-4 shadow">
                    <div class="card-header bg-secondary text-white font-weight-bold">
                    <?= $post->titulo ?>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?= $post->texto ?></p>
                        <a href="<?= URL.'/posts/ver/'.$post->postId ?>" class="btn btn-sm btn-outline-info float-right" data-toggle="tooltip" title="Ler mais...">Ler mais...</a>
                    </div>
                    <div class="card-footer text-muted">
                        <small>Escrito por: <b><?= $post->nome ?></b> em <i><?=Checa::dataBr($post->postDataCadastro) ?></i></small>
                    </div>
                </div>

            <?php endforeach ?>
        </div>
    </div>


</div>