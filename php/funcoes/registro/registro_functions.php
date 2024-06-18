<?php
include '../../bd/acesso_bd.php';


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
function existe_dados_vazios($ma) {
    $sql_check = "SELECT senha FROM tb_profissionais WHERE ma = :ma";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':ma', $ma);
    $stmt_check->execute();
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($result) { // Profissional encontrado
        if (empty($result['senha'])) { // Senha vazia, redirecionar para cadastro de nova senha
            // Implementar redirecionamento para formulário de cadastro de senha
            header("Location: php/primeiro_acesso/primeiro_acesso_aluno.php?ma=$ma&tipo=2");
            exit;
        }
    } else { // Profissional não encontrado, procurar na tabela de alunos
        $sql_check = "SELECT senha FROM tb_alunos WHERE ma_aluno = :ma";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindParam(':ma', $ma);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
        if ($result) { // Aluno encontrado
            if (empty($result['senha'])) { // Senha vazia, redirecionar para formulário de cadastro de senha
                // Implementar redirecionamento para formulário de cadastro de senha
                header("Location: php/primeiro_acesso/primeiro_acesso_aluno.php?ma=$ma&tipo=1");
                exit;
            }
        } else { // Nenhum registro encontrado para o MA
            header("Location: login.php");
            exit;
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

    header("Location: php/login.php");
    exit;
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
    header("Location: ../php/login.php?mensagem=Por favor, preencha todos os campos.");
    exit;
}

// Função para adicionar um novo professor ao banco de dados
function acrescentar_professor($uid_rfid, $nome, $data_nasc, $cpf, $email, $telefone, $cod_genero, $pdo) {
    // Gerar um novo MA (supondo que essa função exista e funcione corretamente)
    $novo_ma = gerar_novo_ma($pdo);

    // Definir a consulta SQL para inserir um novo professor
    $sql = "INSERT INTO tb_profissionais 
            (ma, uid_rfid, nome, data_nascimento, cpf, email, telefone, cod_genero, cod_categoria, cod_status, data_registro) 
            VALUES (:novo_ma, :uid_rfid, :nome, :data_nasc, :cpf, :email, :telefone, :cod_genero, :cod_categoria, :cod_status, NOW())";

    // Preparar os parâmetros para a consulta SQL
    $parameters = array(
        ':novo_ma' => $novo_ma,
        ':uid_rfid' => $uid_rfid,
        ':nome' => $nome,
        ':data_nasc' => $data_nasc,
        ':cpf' => $cpf,
        ':email' => $email,
        ':telefone' => $telefone,
        ':cod_genero' => $cod_genero,
        ':cod_categoria' => 2, // Exemplo: definir o código de categoria para professor
        ':cod_status' => 1 // Exemplo: definir o código de status adequado
    );

    // Preparar e executar a consulta SQL
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameters);

    // Redirecionar para a página inicial após a inserção
    header("Location: ../php/index.php");
    exit; // Encerrar o script após o redirecionamento
}

// Função para adicionar um novo aluno ao banco de dados
function acrescentar_aluno($uid_rfid, $cod_categoria, $cod_curso, $pdo) {
    // Gerar um novo MA (supondo que essa função exista e funcione corretamente)
    $novo_ma = gerar_novo_ma($pdo);

    // Definir a consulta SQL para inserir um novo aluno
    $sql = "INSERT INTO tb_alunos 
            (ma_aluno, uid_rfid, cod_categoria, cod_curso, data_registro) 
            VALUES (:novo_ma, :uid_rfid, :cod_categoria, :cod_curso, NOW())";

    // Preparar os parâmetros para a consulta SQL
    $parameters = array(
        ':novo_ma' => $novo_ma,
        ':uid_rfid' => $uid_rfid,
        ':cod_categoria' => $cod_categoria, // Utiliza o código de categoria fornecido
        ':cod_curso' => $cod_curso // Utiliza o código de curso fornecido
    );

    // Preparar e executar a consulta SQL
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameters);

    // Redirecionar para a página inicial após a inserção
    header("Location: ../php/index.php");
    exit; // Encerrar o script após o redirecionamento
}

