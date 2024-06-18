<?php
include '../php/bd/acesso_bd.php';
include '..\..\funcoes/login\registro_functions.php';

// Recupera o MA do formulário
$ma = $_REQUEST['ma'];

// Verifica se o MA está vazio
if (empty($ma)) {
    header("Location: ../login.php?mensagem=Por favor, insira seu MA.");
    exit; // Encerre o script após a redireção
}

// Chama a função para verificar se o MA está cadastrado e se é o primeiro acesso
if (function_exists('existe_dados_vazios')) {
    existe_dados_vazios($ma, $pdo);
} else {
    die('A função existe_dados_vazios não está definida.');
}

