<?php
// Inclui o arquivo que contém a função existe_dados_vazios e a conexão com o banco de dados
include "acesso_bd.php";
include "functions.php";

    $ma = $_REQUEST['ma'];

    // Chama a função existe_dados_vazios para processar o MA
    existe_dados_vazios($ma, $pdo);

    // Se a função existir_dados_vazios não redirecionar, direciona para login.php
    header("Location: login.php");
    exit;
