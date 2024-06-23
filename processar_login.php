<?php
session_start();
include "acesso_bd.php";
include "functions.php";

// Verifica se os campos foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera o MA e a senha do formulário
    $ma = $_REQUEST['ma'];
    $senha = $_REQUEST['senha'];

    // Chama a função para validar o login
    validar_Login($ma, $senha, $pdo);
} else {
    // Se não foi um POST, redireciona para login.php
    header("Location: login.php");
    exit;
}

