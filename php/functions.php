<?php
include "acesso_bd.php";

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
            handleProfissionalRegistration($uid_rfid, $pdo);
            return; // Retorna após tratamento como profissional
        }

        // Verifica se o ID RFID está cadastrado na tabela de alunos
        $sql_aluno = "SELECT 1 FROM tb_alunos WHERE uid_rfid = :uid_rfid";
        $stmt_aluno = $pdo->prepare($sql_aluno);
        $stmt_aluno->bindParam(':uid_rfid', $uid_rfid);
        $stmt_aluno->execute();

        if ($stmt_aluno->fetchColumn()) {
            echo "UID corresponde a um aluno.\n";
            handleAlunoRegistration($uid_rfid, $pdo);
            return; // Retorna após tratamento como aluno
        }

        // Se não houver correspondência em ambas as tabelas, exibe mensagem
        echo "Nenhum cadastro encontrado para o ID RFID: " . $uid_rfid;
    } catch (PDOException $e) {
        echo "Erro ao executar consulta: " . $e->getMessage();
    }
}








// Função para lidar com registros de profissionais na tabela de presença
function handleProfissionalRegistration($uid_rfid, $pdo) {
    echo "Processando registro de profissional para UID: $uid_rfid\n";
    // Verifica se já existe um registro para o ID RFID na tabela de registros de profissionais
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_profissionais WHERE uid_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$uid_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_profissionais SET data_hora_saida = NOW() WHERE uid_rfid = ? AND id_registro_profissional = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$uid_rfid, $registro['id_registro_profissional']]);
        echo "Horário de saída atualizado para o registro de profissional.\n";
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_profissionais (uid_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$uid_rfid]);
        echo "Novo registro de entrada criado para profissional.\n";
    }
}

// Função para lidar com registros de alunos na tabela de presença
function handleAlunoRegistration($uid_rfid, $pdo) {
    echo "Processando registro de aluno para UID: $uid_rfid\n";
    // Verifica se já existe um registro para o ID RFID na tabela de registros de alunos
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_alunos WHERE uid_rfid = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$uid_rfid]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_alunos SET data_hora_saida = NOW() WHERE uid_rfid = ? AND id_registro_aluno = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$uid_rfid, $registro['id_registro_aluno']]);
        echo "Horário de saída atualizado para o registro de aluno.\n";
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_alunos (uid_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$uid_rfid]);
        echo "Novo registro de entrada criado para aluno.\n";
    }
}

//Verificar se o RA bate com o banco de dados
function validateRA($ra, $pdo){



    firstAccess($ra, $pdo);

}

//Realiza o cadastro das informações faltantes no banco de dados
function firstAccess($uid_rfid, $pdo){






}