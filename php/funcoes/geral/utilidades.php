<?php
include "acesso_bd.php";
include "funcoes/registro/registro_functions.php";
include "funcoes/login/login_functions.php";


// Função para verificar o ID RFID e chamar a função de registro apropriada
function checkAndHandleRegistration($uid_rfid, $pdo) {
    echo "Verificando UID: $uid_rfid\n";

    $uid_rfid = trim($uid_rfid);

    try {
        // Verifica se o ID RFID está cadastrado na tabela de profissionais
        $sql_profissional = "SELECT 1 FROM tb_profissionais WHERE uid_rfid = :uid_rfid";
        $stmt_profissional = $pdo->prepare($sql_profissional);
        $stmt_profissional->bindParam(':uid_rfid', $uid_rfid);
        $stmt_profissional->execute();

        if ($stmt_profissional->fetchColumn()) {
            echo "UID corresponde a um profissional.\n";
            entrada_saida_profissionais($uid_rfid, $pdo);
            return; // Retorna após tratamento como profissional
        }

        // Verifica se o ID RFID está cadastrado na tabela de alunos
        $sql_aluno = "SELECT 1 FROM tb_alunos WHERE uid_rfid = :uid_rfid";
        $stmt_aluno = $pdo->prepare($sql_aluno);
        $stmt_aluno->bindParam(':uid_rfid', $uid_rfid);
        $stmt_aluno->execute();

        if ($stmt_aluno->fetchColumn()) {
            echo "UID corresponde a um aluno.\n";
            entrada_saida_alunos($uid_rfid, $pdo);
            return; // Retorna após tratamento como aluno
        }

        // Se não houver correspondência em ambas as tabelas, exibe mensagem
        echo "Nenhum cadastro encontrado para o ID RFID: " . $uid_rfid;
    } catch (PDOException $e) {
        echo "Erro ao executar consulta: " . $e->getMessage();
    }
}

