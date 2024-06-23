<?php
include "acesso_bd.php";
include "functions.php";

// Recupera os dados do formulário
$ma = $_POST['ma'];
$telefone = $_POST['telefone'];
$data_nasc = $_POST['data_nascimento'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$cod_genero = $_POST['genero'];

// Verifica se algum campo está vazio
if (empty($ma) || empty($data_nasc) || empty($cod_genero) || empty($cpf) || empty($email) || empty($senha)) {
    header("Location: primeiro_acesso_aluno.php?ma=$ma&mensagem=Por favor, preencha todos os campos antes de enviar.");
    exit; // Encerra o script após a redireção
}

// Transforma a senha em MD5 antes de passar para a função
$senha_md5 = md5($senha);

// Como é o primeiro acesso do aluno, insere as informações restantes no BD
primeiro_acesso_aluno($ma, $cod_genero, $telefone, $data_nasc, $cpf, $email, $senha_md5, $pdo);

unset($_SESSION['ma']);
