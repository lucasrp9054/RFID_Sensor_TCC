<?php
// Inclui o arquivo que contém a função existe_dados_vazios e a conexão com o banco de dados
include "acesso_bd.php";
include "functions.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma = $_POST['ma'];
    $cod_tipo_usuario = $_POST['tipo_usuario'];

    if (upload_imagem($ma, $cod_tipo_usuario, $pdo)) {
        header("Location: profile.php");
    } else {
        echo '<div class="alert alert-danger text-center">Erro ao carregar a imagem.</div>';
    }
}
