<?php
include '../bd/acesso_bd.php';
include '../funcoes/login/login_functions.php';

// Recupera o MA e a senha do formulário
$ma = $_REQUEST['ma'];
$senha = $_REQUEST['senha'];

// Verifica se o MA ou a senha estão vazios
if (empty($ma) || empty($senha)) {
    header("Location: ../login.php?mensagem=Por favor, preencha ambos os campos.");
    exit; // Encerre o script após a redireção
}

// Chama a função para validar o login
validar_Login($ma, $senha, $pdo);

