<?php
include 'banco_de_dados.php';
include 'funcoes.php';

// Recupera o MA e a senha do formulário
$ma = $_REQUEST['ma'];
$senha = $_REQUEST['senha'];

if (empty($ma) || empty($senha)) {
    header("Location: login.php?mensagem=Por favor, preencha ambos os campos.");
    exit; // Encerre o script após a redireção
}

// Verifica se o MA e a senha são válidos
validar_Login($ma, $senha, $pdo);

