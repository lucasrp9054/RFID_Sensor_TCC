<?php
// Inclui os arquivos necessários
include "acesso_bd.php";
include "functions.php";

// Verifica se os dados do formulário foram recebidos corretamente
if (isset($_REQUEST['uid_rfid'], $_REQUEST['nome'], $_REQUEST['cod_curso'])) {

    // Recupera os dados do formulário
    $uid_rfid = $_REQUEST['uid_rfid'];
    $nome = $_REQUEST['nome'];
    $cod_categoria = '1';
    $cod_curso = $_REQUEST['cod_curso'];

    try {
        // Chama a função para acrescentar um novo aluno
        acrescentar_aluno($nome, $uid_rfid, $cod_categoria, $cod_curso, $pdo);

        // Redireciona para a página inicial após o registro
        header("Location: form_novo_aluno.php?mensagem=Aluno cadastrado com sucesso.");
        exit; // Encerra o script após o redirecionamento
    } catch (Exception $e) {
        // Em caso de erro, redireciona para a página com mensagem de erro
        header("Location: form_novo_aluno.php?mensagem=Erro ao cadastrar o aluno: " . $e->getMessage());
        exit; // Encerra o script após o redirecionamento
    }
} else {
    // Se algum parâmetro estiver faltando, redireciona para a página com mensagem de erro
    header("Location: form_novo_aluno.php?mensagem=Por favor, preencha todos os campos.");
    exit; // Encerra o script após o redirecionamento
}

