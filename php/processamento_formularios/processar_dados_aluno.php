<?php
include '../bd/acesso_bd.php';
include '../funcoes/login/registro_functions.php';

// Recupera os dados do formulário
$ma = $_REQUEST['ma'];
$nome = $_REQUEST['nome'];
$data_nasc = $_REQUEST['data_nasc'];
$cpf = $_REQUEST['cpf'];
$email = $_REQUEST['email'];
$telefone = $_REQUEST['telefone'];
$senha = $_REQUEST['senha'];

// Verifica se algum campo está vazio
if (empty($ma) || empty($nome) || empty($data_nasc) || empty($cpf) || empty($email) || empty($telefone) || empty($senha)) {
    header("Location: ../login.php?mensagem=Por favor, preencha todos os campos antes de enviar.");
    exit; // Encerre o script após a redireção
}

// Verifica se o MA está cadastrado e se é seu primeiro acesso
primeiro_acesso_aluno($ma, $nome, $data_nasc, $cpf, $email, $telefone, $senha, $pdo);

