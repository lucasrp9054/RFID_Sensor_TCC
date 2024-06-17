<?php
include "acesso_bd.php";


// Função para lidar com registros de profissionais na tabela de presença
function entrada_saida_profissionais($uid_rfid, $pdo) {
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
        echo "Horário de saída atualizado para o registro de presença do profissional.\n";
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_profissionais (uid_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$uid_rfid]);
        echo "Novo registro de presença criado para profissional.\n";
    }
}

// Função para lidar com registros de alunos na tabela de presença
function entrada_saida_alunos($uid_rfid, $pdo) {
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
        echo "Horário de saída atualizado para o registro de presença do aluno.\n";
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_alunos (uid_rfid, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$uid_rfid]);
        echo "Novo registro de presença criado para aluno.\n";
    }
}

// Função para verificar se é o primeiro acesso do profissional/aluno e redirecionar conforme necessário
function existe_dados_vazios($ma, $pdo) {

    $sql_check = "SELECT senha FROM tb_profissionais WHERE ma = :ma";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':ma', $ma);
    $stmt_check->execute();
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($result) { // Profissional encontrado

        if (empty($result['senha'])) { // Senha vazia, redirecionar para cadastro de nova senha
            // Implementar redirecionamento
        }
    }
    else
    { // Profissional não encontrado, procurar na tabela de alunos
        $sql_check = "SELECT senha FROM tb_alunos WHERE ma_aluno = :ma";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':ma', $ma);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
        if ($result) { // Aluno encontrado

            if (empty($result['senha'])) { // Senha vazia, redirecionar para formulário de cadastro
                // Implementar redirecionamento
            }
        }
        else
        {
            return 0; // Não é o primeiro acesso desse MA, tentar fazer o login
        }
    }
    
}

// Função para definir a senha inicial do profissional no primeiro acesso
function primeiro_acesso_profissional($ma, $senha, $pdo) {
    
    $senha_md5 = md5($senha); // Transformando a senha em md5
    
    $sql = "UPDATE tb_profissionais SET senha = :senha WHERE ma = :ma";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':senha', $senha_md5);
    $stmt->bindParam(':ma', $ma);
    $stmt->execute();
}
    
// Função para definir as informações iniciais do aluno no primeiro acesso
function primeiro_acesso_aluno($ma, $nome, $data_nasc, $cpf, $email, $telefone, $senha, $pdo) {
    $senha_md5 = md5($senha); // Transformando a senha em md5
    
    $sql = "UPDATE tb_alunos 
            SET nome = :nome, data_nascimento = :data_nasc, cpf = :cpf, email = :email, telefone = :telefone, senha = :senha 
            WHERE ma_aluno = :ma";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':data_nasc', $data_nasc);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':senha', $senha_md5);
    $stmt->bindParam(':ma', $ma);
    $stmt->execute();
    
    // Redirecionar para a página de login
    
    header("Location: index.php");
}