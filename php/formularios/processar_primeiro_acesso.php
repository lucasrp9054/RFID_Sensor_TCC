<?php
// Inclui o arquivo que contém a função existe_dados_vazios e a conexão com o banco de dados
include "acesso_bd.php";
include "php/funcoes/registro/registro_functions.php";


    $ma = $_REQUEST['ma'];
        
    // Chama a função existe_dados_vazios para processar o MA
    existe_dados_vazios($ma, $pdo);

    // Exemplo de redirecionamento após o processamento
    header("Location: login.php");
