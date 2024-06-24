<?php
// Inclui os arquivos necessários
include "acesso_bd.php";
include "functions.php";

// Verifica se os dados foram recebidos corretamente
if (
    isset($_REQUEST['uid_rfid']) && 
    isset($_REQUEST['nome']) && 
    isset($_REQUEST['data_nasc']) && 
    isset($_REQUEST['cpf']) && 
    isset($_REQUEST['email']) && 
    isset($_REQUEST['telefone']) && 
    isset($_REQUEST['cod_genero']) && 
    isset($_REQUEST['cod_area'])
) {
    // Recupera os dados do formulário
    $uid_rfid = $_REQUEST['uid_rfid'];
    $nome = $_REQUEST['nome'];
    $data_nasc = $_REQUEST['data_nasc'];
    $cpf = $_REQUEST['cpf'];
    $email = $_REQUEST['email'];
    $telefone = $_REQUEST['telefone'];
    $cod_genero = $_REQUEST['cod_genero'];
    $cod_area = $_REQUEST['cod_area'];

    // Verifica se algum dos campos essenciais está vazio
    if (empty($uid_rfid) || empty($nome) || empty($data_nasc) || empty($cpf) || empty($email) || empty($telefone) || empty($cod_genero)) {
        header("Location: ../login.php?mensagem=Por favor, preencha todos os campos antes de enviar.");
        exit; // Encerra o script após o redirecionamento
    }

    // Chama a função para acrescentar um novo professor
    acrescentar_professor($uid_rfid, $nome, $data_nasc, $cpf, $email, $telefone, $cod_genero,$cod_area, $pdo);

    // Redireciona para a página inicial após o registro
    header("Location: index.php");
    exit; // Encerra o script após o redirecionamento
} else {
    // Se algum parâmetro estiver faltando, redireciona para a página de login com mensagem de erro
    header("Location: ../login.php?mensagem=Por favor, preencha todos os campos.");
    exit; // Encerra o script após o redirecionamento
}

