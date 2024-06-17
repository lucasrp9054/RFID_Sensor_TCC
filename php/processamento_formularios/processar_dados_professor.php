<?php
include '../bd/acesso_bd.php';
include '../funcoes/login/registro_functions.php';

// Recupera os dados do formulário
$ma = $_REQUEST['ma'];
$senha = $_REQUEST['senha'];

// Verifica se o MA ou a senha estão vazios
if (empty($ma) || empty($senha)) {
    header("Location: ../login.php?mensagem=Por favor, preencha ambos os campos.");
    exit; // Encerre o script após a redireção
}

// Verifica se o MA está cadastrado e atualiza sua senha no BD
primeiro_acesso_profissional($ma, $senha, $pdo);

