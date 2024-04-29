<?php

include "acesso_bd.php";

// Função para ler os dados da porta serial
function readSerial() {
    $port = fopen("COM3", "r"); // Atualize com a porta serial do Arduino
    $data = fgets($port);
    fclose($port);
    return $data;
}

// Função para verificar o ID RFID e chamar a função de registro apropriada
function checkAndHandleRegistration($id_rfid, $pdo) {
    // Verifica se o ID RFID está cadastrado no sistema
    $stmt = $pdo->prepare("SELECT cod_categoria FROM tb_cadastro WHERE id_rfid = ?");
    $stmt->execute([$id_rfid]);
    $cadastro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cadastro) {
        $categoria = $cadastro['cod_categoria'];

        if ($categoria == 1) {
            handleProfessorRegistration($id_rfid, $pdo);
        } elseif ($categoria == 2) {
            handleAlunoRegistration($id_rfid, $pdo);
        } else {
            echo "Categoria inválida para o ID RFID: " . $id_rfid;
        }
    } else {
        echo "Nenhum cadastro encontrado para o ID RFID: " . $id_rfid;
    }
}

// Função para lidar com registros de professores
function handleProfessorRegistration($id_rfid, $pdo) {
    // Verifica se já existe um registro para o ID RFID na tabela de registros de professores
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_professores WHERE id_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$id_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_professores SET data_hora_saida = NOW() WHERE id_rfid = ? AND id_registro_professor = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$id_rfid, $registro['id_registro_professor']]);
        echo "Horário de saída atualizado no registro do ID RFID: " . $id_rfid;
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_professores (id_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$id_rfid]);
        echo "Novo registro de entrada inserido no banco de dados para o ID RFID: " . $id_rfid;
    }
}

// Função para lidar com registros de alunos
function handleAlunoRegistration($id_rfid, $pdo) {
    // Verifica se já existe um registro para o ID RFID na tabela de registros de alunos
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_alunos WHERE id_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$id_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_alunos SET data_hora_saida = NOW() WHERE id_rfid = ? AND id_registro_aluno = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$id_rfid, $registro['id_registro_aluno']]);
        echo "Horário de saída atualizado no registro do ID RFID: " . $id_rfid;
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_alunos (id_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$id_rfid]);
        echo "Novo registro de entrada inserido no banco de dados para o ID RFID: " . $id_rfid;
    }
}

try {
    while (true) {
        // Lê o ID enviado pelo Arduino
        $id_rfid = readSerial();

        // Chama a função para verificar e lidar com o registro
        checkAndHandleRegistration($id_rfid, $pdo);
    }
} catch (Exception $e) {
    echo 'Exceção capturada: ',  $e->getMessage(), "\n";
}

?>
