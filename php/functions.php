<?php

    include "acesso_bd.php";


    // Função para verificar o ID RFID e chamar a função de registro apropriada
function checkAndHandleRegistration($uid_rfid, $pdo) {
    // Verifica se o ID RFID está cadastrado no sistema
    $stmt = $pdo->prepare("SELECT cod_categoria FROM tb_cadastro WHERE uid_rfid = ?");
    $stmt->execute([$uid_rfid]);
    $cadastro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cadastro) {
        $categoria = $cadastro['cod_categoria'];

        if ($categoria == 1) {
            handleProfessorRegistration($uid_rfid, $pdo);
        } elseif ($categoria == 2) {
            handleAlunoRegistration($uid_rfid, $pdo);
        } else {
            echo "Categoria inválida para o ID RFID: " . $uid_rfid;
        }
    } else {
        echo "Nenhum cadastro encontrado para o ID RFID: " . $uid_rfid;
    }
}

// Função para lidar com registros de professores
function handleProfessorRegistration($uid_rfid, $pdo) {
    // Verifica se já existe um registro para o ID RFID na tabela de registros de professores
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_professores WHERE uid_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$uid_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_professores SET data_hora_saida = NOW() WHERE uid_rfid = ? AND id_registro_professor = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$uid_rfid, $registro['id_registro_professor']]);
        echo "Horário de saída atualizado no registro do ID RFID: " . $uid_rfid;
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_professores (id_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$uid_rfid]);
        echo "Novo registro de entrada inserido no banco de dados para o ID RFID: " . $uid_rfid;
    }
}

// Função para lidar com registros de alunos
function handleAlunoRegistration($id_rfid, $pdo) {
    // Verifica se já existe um registro para o ID RFID na tabela de registros de alunos
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_alunos WHERE uid_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$id_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_alunos SET data_hora_saida = NOW() WHERE uid_rfid = ? AND id_registro_aluno = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$id_rfid, $registro['id_registro_aluno']]);
        echo "Horário de saída atualizado no registro do ID RFID: " . $id_rfid;
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_alunos (uid_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$id_rfid]);
        echo "Novo registro de entrada inserido no banco de dados para o ID RFID: " . $id_rfid;
    }
}

