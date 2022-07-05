<?php
//error_reporting — Define quais erros serão reportados
error_reporting(0);
ini_set('error_reporting', 0);
const LOG = true;

//função para personalizar os erros
function phpErro($erro, $mensagem, $arquivo, $linha)
{

    switch ($erro):
        case 2;
            $css = 'alert-warning';
            break;
        case 8;
            $css = 'alert-primary';
            break;
        case 1;
        case 256;
        case 2002;
        case 1045;
        case 1049;
            $css = 'alert-danger';
            break;
        default:
            $css = '';
    endswitch;

    echo "<p class=\"alert {$css} m-2\"><b>Erro:</b> {$mensagem} <b>no arquivo</b> {$arquivo} <b>na linha</b> <strong class=\"text-danger\">{$linha}</strong></p>";

    if (LOG) :
        $logs = "Erro: {$mensagem} no arquivo {$arquivo} na linha {$linha}\n";
        error_log($logs, 3, "" . dirname(__FILE__) . "/logs/phperro.log");
    endif;

    if ($erro == 1 || $erro == 256) :
        die();
    endif;
}

//set_error_handler — Define uma função do usuário para manipular erros
set_error_handler('phpErro');
