<?php
include "acesso_bd.php"; // Inclui o arquivo de acesso ao banco de dados
include "funcoes/registro/registro_functions.php"; // Inclui as funções relacionadas ao registro
include "funcoes/login/login_functions.php"; // Inclui as funções relacionadas ao login

/**
 * Função para verificar o ID RFID e chamar a função de registro apropriada
 * @param string $uid_rfid O ID RFID a ser verificado
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados
 */
function verificar_e_tratar_registro($uid_rfid, $pdo) {
    echo "Verificando UID: $uid_rfid\n";

    $uid_rfid = trim($uid_rfid); // Remove espaços em branco extras do UID RFID

    try {
        // Verifica se o ID RFID está cadastrado na tabela de profissionais
        $sql_profissional = "SELECT 1 FROM tb_profissionais WHERE uid_rfid = :uid_rfid";
        $stmt_profissional = $pdo->prepare($sql_profissional);
        $stmt_profissional->bindParam(':uid_rfid', $uid_rfid);
        $stmt_profissional->execute();

        if ($stmt_profissional->fetchColumn()) {
            echo "UID corresponde a um profissional.\n";
            entrada_saida_profissionais($uid_rfid, $pdo); // Chama a função de registro de entrada/saída para profissionais
            return; // Retorna após tratamento como profissional
        }

        // Verifica se o ID RFID está cadastrado na tabela de alunos
        $sql_aluno = "SELECT 1 FROM tb_alunos WHERE uid_rfid = :uid_rfid";
        $stmt_aluno = $pdo->prepare($sql_aluno);
        $stmt_aluno->bindParam(':uid_rfid', $uid_rfid);
        $stmt_aluno->execute();

        if ($stmt_aluno->fetchColumn()) {
            echo "UID corresponde a um aluno.\n";
            entrada_saida_alunos($uid_rfid, $pdo); // Chama a função de registro de entrada/saída para alunos
            return; // Retorna após tratamento como aluno
        }

        // Se não houver correspondência em ambas as tabelas, exibe mensagem
        echo "Nenhum cadastro encontrado para o ID RFID: " . $uid_rfid;
    } catch (PDOException $e) {
        echo "Erro ao executar consulta: " . $e->getMessage(); // Exibe mensagem de erro se ocorrer uma exceção PDO
    }
}

/**
 * Função para gerar um novo MA único que não exista nas tabelas tb_alunos e tb_profissionais
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados
 * @return string O MA gerado e único
 */
function gerar_novo_ma($pdo) {
    $ma_gerado = ''; // Variável para armazenar o MA gerado

    // Gerar um MA aleatório de 8 caracteres (pode ser ajustado conforme necessário)
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Caracteres possíveis para o MA
    $tamanho_ma = 8; // Tamanho do MA

    do {
        // Gerar o MA aleatório
        $ma_gerado = '';
        for ($i = 0; $i < $tamanho_ma; $i++) {
            $ma_gerado .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        // Verificar se o MA já existe na tabela tb_alunos
        $sql_check_aluno = "SELECT COUNT(*) as count FROM tb_alunos WHERE ma = :ma_aluno";
        $stmt_check_aluno = $pdo->prepare($sql_check_aluno);
        $stmt_check_aluno->bindParam(':ma_aluno', $ma_gerado);
        $stmt_check_aluno->execute();
        $result_aluno = $stmt_check_aluno->fetch(PDO::FETCH_ASSOC);

        // Verificar se o MA já existe na tabela tb_profissionais
        $sql_check_profissional = "SELECT COUNT(*) as count FROM tb_profissionais WHERE ma = :ma";
        $stmt_check_profissional = $pdo->prepare($sql_check_profissional);
        $stmt_check_profissional->bindParam(':ma', $ma_gerado);
        $stmt_check_profissional->execute();
        $result_profissional = $stmt_check_profissional->fetch(PDO::FETCH_ASSOC);

        // Se ambos retornarem 0, significa que o MA é único e pode ser utilizado
        if ($result_aluno['count'] == 0 && $result_profissional['count'] == 0) {
            break; // Sai do loop, pois encontramos um MA único
        }
        // Se não for único, iremos gerar outro MA na próxima iteração do loop
    } while (true);

    return $ma_gerado; // Retorna o MA gerado e único
}

