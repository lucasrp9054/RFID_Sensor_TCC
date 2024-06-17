<?php
include 'bd/acesso_bd.php';

include "funcoes\logout\logout_functions.php"; // Inclui as funções relacionadas ao logout
include "funcoes/registro/registro_functions.php"; // Inclui as funções relacionadas ao registro
include '../funcoes/geral/utilidades.php';



// Função para validar o login de usuário (aluno, professor, coordenação)
function validar_Login($ma, $senha, $pdo) {
    $senha_md5 = md5($senha); // Transformando a senha inserida em md5

    // Verificar se é aluno
    $sql_check_aluno = "SELECT senha FROM tb_alunos WHERE ma_aluno = :ma";
    $stmt_check_aluno = $pdo->prepare($sql_check_aluno);
    $stmt_check_aluno->bindParam(':ma', $ma);
    $stmt_check_aluno->execute();
    $result_aluno = $stmt_check_aluno->fetch(PDO::FETCH_ASSOC);

    if ($result_aluno && $result_aluno['senha'] === $senha_md5) { // Senha corresponde para aluno
        realizar_login($ma, 'aluno');
    }

    // Verificar se é professor
    $sql_check_professor = "SELECT senha FROM tb_profissionais WHERE ma = :ma AND cod_categoria = 2";
    $stmt_check_professor = $pdo->prepare($sql_check_professor);
    $stmt_check_professor->bindParam(':ma', $ma);
    $stmt_check_professor->execute();
    $result_professor = $stmt_check_professor->fetch(PDO::FETCH_ASSOC);

    if ($result_professor && $result_professor['senha'] === $senha_md5) { // Senha corresponde para professor
        realizar_login($ma, 'professor');
    }

    // Verificar se é coordenação
    $sql_check_coordenacao = "SELECT senha, cod_categoria FROM tb_profissionais WHERE ma = :ma AND cod_categoria = 3";
    $stmt_check_coordenacao = $pdo->prepare($sql_check_coordenacao);
    $stmt_check_coordenacao->bindParam(':ma', $ma);
    $stmt_check_coordenacao->execute();
    $result_coordenacao = $stmt_check_coordenacao->fetch(PDO::FETCH_ASSOC);

    if ($result_coordenacao && $result_coordenacao['senha'] === $senha_md5) { // Senha corresponde para coordenação
        // Buscar a área associada ao coordenador
        $sql_area_coordenacao = "SELECT cod_area FROM tb_coordenacao_area WHERE ma_prof = :ma";
        $stmt_area_coordenacao = $pdo->prepare($sql_area_coordenacao);
        $stmt_area_coordenacao->bindParam(':ma', $ma);
        $stmt_area_coordenacao->execute();
        $result_area = $stmt_area_coordenacao->fetch(PDO::FETCH_ASSOC);

        if ($result_area)
        {
            realizar_login($ma, 'coordenacao', $result_area['cod_area']);
        }
        else
        {
            header("Location: login.php?mensagem=Coordenador encontrado, mas área não especificada.");
            exit;
        }
    }

    // Se nenhum usuário foi encontrado ou senha incorreta, exibir mensagem de erro
    header("Location: login.php?mensagem=MA não encontrado ou senha incorreta.");
    exit;
}

// Função para realizar o login do usuário e iniciar a sessão
function realizar_login($ma, $tipo_usuario, $cod_area = null) {
    session_start();
    $_SESSION['user'] = $ma;
    $_SESSION['tipo_usuario'] = $tipo_usuario;

    if ($cod_area !== null) {// Se for um coordenador, define a área supervisionada

        $_SESSION['cod_area'] = $cod_area;
    }
    header("Location: index.php");
    exit;
}