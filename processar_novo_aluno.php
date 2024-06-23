<?php
// Inclui os arquivos necessários
include "acesso_bd.php";
include "functions.php";

// Verifica se os dados do formulário foram recebidos corretamente
if (isset($_REQUEST['uid_rfid'],$_REQUEST['nome'],
          $_REQUEST['cod_curso']))
{

    // Recupera os dados do formulário
    $uid_rfid = $_REQUEST['uid_rfid'];
    $nome = $_REQUEST['nome'];
    $cod_categoria = '1';
    $cod_curso = $_REQUEST['curso'];
    

    // Verifica se algum campo obrigatório está vazio
    if (empty($uid_rfid) || empty($cod_curso)) {
        header("Location: form_novo_aluno.php?mensagem=Por favor, preencha todos os campos.");
        exit; // Encerra o script após o redirecionamento
    }

    // Chama a função para acrescentar um novo aluno
    acrescentar_aluno($nome, $uid_rfid, $cod_categoria, $cod_curso, $pdo);

    // Redireciona para a página inicial após o registro
    header("Location: form_novo_aluno.php");
    exit; // Encerra o script após o redirecionamento
} else {
    // Se algum parâmetro estiver faltando, redireciona para a página de login com mensagem de erro
    header("Location: form_novo_aluno.php?mensagem=Por favor, preencha todos os campos.");
    exit; // Encerra o script após o redirecionamento
}

