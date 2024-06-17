<?php
// Inclui os arquivos necessários
include '../bd/acesso_bd.php'; // Verifique o caminho correto do arquivo acesso_bd.php
include '../funcoes/registro/registro_functions.php'; // Verifique o caminho correto do arquivo registro_functions.php

// Verifica se os dados do formulário foram recebidos corretamente
if (isset($_REQUEST['uid_rfid'],
          $_REQUEST['cod_categoria'],
          $_REQUEST['cod_curso']))
{

    // Recupera os dados do formulário
    $uid_rfid = $_REQUEST['uid_rfid'];
    $cod_categoria = $_REQUEST['cod_categoria'];
    $cod_curso = $_REQUEST['cod_curso'];

    // Verifica se algum campo obrigatório está vazio
    if (empty($uid_rfid) || empty($cod_categoria) || empty($cod_curso)) {
        header("Location: ../login.php?mensagem=Por favor, preencha todos os campos.");
        exit; // Encerra o script após o redirecionamento
    }

    // Chama a função para acrescentar um novo aluno
    acrescentar_aluno($uid_rfid, $cod_categoria, $cod_curso, $pdo);

    // Redireciona para a página inicial após o registro
    header("Location: index.php");
    exit; // Encerra o script após o redirecionamento
} else {
    // Se algum parâmetro estiver faltando, redireciona para a página de login com mensagem de erro
    header("Location: ../login.php?mensagem=Por favor, preencha todos os campos.");
    exit; // Encerra o script após o redirecionamento
}

