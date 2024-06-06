<?php

include "acesso_bd.php";

// Função para verificar o ID RFID e chamar a função de registro apropriada
function checkAndHandleRegistration($uid_rfid, $pdo) {
    // Verifica se o ID RFID está cadastrado na tabela de profissionais
    $stmt = $pdo->prepare("SELECT 1 FROM tb_profissionais WHERE uid_rfid = ?");
    $stmt->execute([$uid_rfid]);
    $isProfissional = $stmt->fetchColumn();

    if ($isProfissional) {
        handleProfissionalRegistration($uid_rfid, $pdo);
    } else {
        // Verifica se o ID RFID está cadastrado na tabela de alunos
        $stmt = $pdo->prepare("SELECT 1 FROM tb_alunos WHERE uid_rfid = ?");
        $stmt->execute([$uid_rfid]);
        $isAluno = $stmt->fetchColumn();

        if ($isAluno) {
            handleAlunoRegistration($uid_rfid, $pdo);
        } else {
            // Se não houver correspondência, exibe uma mensagem
            // indicando que nenhum cadastro foi encontrado
            echo "Nenhum cadastro encontrado para o ID RFID: " . $uid_rfid;
        }
    }
}

// Função para lidar com registros de profissionais
function handleProfissionalRegistration($uid_rfid, $pdo) {
    // Verifica se já existe um registro para o ID RFID na tabela de registros de profissionais
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_profissionais WHERE uid_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$uid_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_profissionais SET data_hora_saida = NOW() WHERE uid_rfid = ? AND id_registro_profissional = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$uid_rfid, $registro['id_registro_profissional']]);

    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_profissionais (uid_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$uid_rfid]);
        
    }
}

// Função para lidar com registros de alunos
function handleAlunoRegistration($uid_rfid, $pdo) {
    // Verifica se já existe um registro para o ID RFID na tabela de registros de alunos
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_alunos WHERE uid_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$uid_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_alunos SET data_hora_saida = NOW() WHERE uid_rfid = ? AND id_registro_aluno = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$uid_rfid, $registro['id_registro_aluno']]);

    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_alunos (uid_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$uid_rfid]);
        
    }
}

