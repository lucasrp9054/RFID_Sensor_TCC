<?php

include "acesso_bd.php";

// Função para validar o login de usuário (aluno, professor, coordenação)
function validar_login($ma, $senha, $pdo) {
    // Verificar se é aluno
    $sql_check_aluno = "SELECT data_conclusao, senha FROM tb_alunos WHERE ma_aluno = :ma";
    $stmt_check_aluno = $pdo->prepare($sql_check_aluno);
    $stmt_check_aluno->bindParam(':ma', $ma);
    $stmt_check_aluno->execute();
    $result_aluno = $stmt_check_aluno->fetch(PDO::FETCH_ASSOC);

    if ($result_aluno) {
        // Verifica se há data de conclusão definida
        if (!empty($result_aluno['data_conclusao'])) {
            header("Location: login.php?mensagem=Você já concluiu o curso e não possui mais acesso.");
            exit; // Encerra o script após o redirecionamento
        }

        // Verifica se a senha está correta
        if ($result_aluno['senha'] === md5($senha)) {
            realizar_login($ma, 'Aluno');
        } else {
            // Senha incorreta
            header("Location: login.php?mensagem=Senha incorreta para aluno.");
            exit; // Encerra o script após o redirecionamento
        }
    }

    // Verificar se é professor
    $sql_check_professor = "SELECT cod_status, senha FROM tb_profissionais WHERE ma = :ma AND cod_categoria = 2";
    $stmt_check_professor = $pdo->prepare($sql_check_professor);
    $stmt_check_professor->bindParam(':ma', $ma);
    $stmt_check_professor->execute();
    $result_professor = $stmt_check_professor->fetch(PDO::FETCH_ASSOC);

    if ($result_professor) {
        // Verifica se a conta está inativa
        if ($result_professor['cod_status'] === 2) {
            header("Location: login.php?mensagem=Esta conta foi inativada.");
            exit; // Encerra o script após o redirecionamento
        }
    
        // Verifica se a senha está correta
        if ($result_professor['senha'] === md5($senha)) {
            realizar_login($ma, 'Professor');
        } else {
            // Senha incorreta
            header("Location: login.php?mensagem=Senha incorreta para professor.");
            exit; // Encerra o script após o redirecionamento
        }
    }

    // Verificar se é coordenação
    $sql_check_coordenacao = "SELECT cod_status, senha, cod_categoria FROM tb_profissionais WHERE ma = :ma AND cod_categoria = 3";
    $stmt_check_coordenacao = $pdo->prepare($sql_check_coordenacao);
    $stmt_check_coordenacao->bindParam(':ma', $ma);
    $stmt_check_coordenacao->execute();
    $result_coordenacao = $stmt_check_coordenacao->fetch(PDO::FETCH_ASSOC);

    if ($result_coordenacao) {
        // Verifica se a conta está inativa
        if ($result_coordenacao['cod_status'] === 2) {
            header("Location: login.php?mensagem=Esta conta foi inativada.");
            exit; // Encerra o script após o redirecionamento
        }

        // Verifica se a senha está correta
        if ($result_coordenacao['senha'] === md5($senha)) {
            // Buscar a área associada ao coordenador
            $sql_area_coordenacao = "SELECT cod_area FROM tb_coordenacao_area WHERE ma_coordenacao = :ma";
            $stmt_area_coordenacao = $pdo->prepare($sql_area_coordenacao);
            $stmt_area_coordenacao->bindParam(':ma', $ma);
            $stmt_area_coordenacao->execute();
            $result_area = $stmt_area_coordenacao->fetch(PDO::FETCH_ASSOC);

            if ($result_area) {
                realizar_login($ma, 'Coordenacao', $result_area['cod_area']);
            } else {
                header("Location: login.php?mensagem=Coordenador encontrado, mas área não especificada.");
                exit; // Encerra o script após o redirecionamento
            }
        } else {
            // Senha incorreta
            header("Location: login.php?mensagem=Senha incorreta para coordenação.");
            exit; // Encerra o script após o redirecionamento
        }
    }

    // Se nenhum usuário foi encontrado ou senha incorreta, exibir mensagem de erro
    header("Location: login.php?mensagem=MA não encontrado ou senha incorreta.");
    exit; // Encerra o script após o redirecionamento
}


// Função para realizar o login do usuário e iniciar a sessão
function realizar_login($ma, $tipo_usuario, $cod_area = null) {
    // Define as variáveis de sessão
    $_SESSION['user'] = $ma;
    $_SESSION['tipo_usuario'] = $tipo_usuario;

    // Se for um coordenador, define a área supervisionada
    if ($cod_area !== null) {
        $_SESSION['cod_area'] = $cod_area;
    }

    // Redireciona para o index.php
    header("Location: index.php");
    exit; // Certifique-se de que o script termina após o redirecionamento
}



//////////////////////////////////////////////////////////


/**
 * Função para verificar o ID RFID e chamar a função de registro apropriada
 * @param string $uid_rfid O ID RFID a ser verificado
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados
 */
function verificar_e_tratar_registro($uid_rfid, $pdo) {//Funcionando
    echo "Verificando UID: $uid_rfid\n";

    $uid_rfid = trim($uid_rfid); // Remove espaços em branco extras do UID RFID

    try {
        // Verifica se o ID RFID está cadastrado na tabela de profissionais
        $sql_profissional = "SELECT ma FROM tb_profissionais WHERE uid_rfid = :uid_rfid";
        $stmt_profissional = $pdo->prepare($sql_profissional);
        $stmt_profissional->bindParam(':uid_rfid', $uid_rfid);
        $stmt_profissional->execute();

        if ($row = $stmt_profissional->fetch(PDO::FETCH_ASSOC)) {
            $ma_profissional = $row['ma'];
            echo "UID corresponde a um profissional.\n";
            entrada_saida_profissionais($ma_profissional, $pdo); // Chama a função de registro de entrada/saída para profissionais usando o MA
            return; // Retorna após tratamento como profissional
        }


        // Verifica se o ID RFID está cadastrado na tabela de alunos
        $sql_aluno = "SELECT ma_aluno FROM tb_alunos WHERE uid_rfid = :uid_rfid";
        $stmt_aluno = $pdo->prepare($sql_aluno);
        $stmt_aluno->bindParam(':uid_rfid', $uid_rfid);
        $stmt_aluno->execute();

        if ($row = $stmt_aluno->fetch(PDO::FETCH_ASSOC)) {
            $ma_aluno = $row['ma_aluno'];
            echo "UID corresponde a um aluno.\n";
            entrada_saida_alunos($ma_aluno, $pdo); // Chama a função de registro de entrada/saída para alunos
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
    $tamanho_ma = 3; // Tamanho do MA

    do {
        // Gerar o MA aleatório
        $ma_gerado = '';
        for ($i = 0; $i < $tamanho_ma; $i++) {
            $ma_gerado .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        // Verificar se o MA já existe na tabela tb_alunos
        $sql_check_aluno = "SELECT COUNT(*) as count FROM tb_alunos WHERE ma_aluno = :ma_aluno";
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

/////////////////////////////////////


// Função para lidar com registros de profissionais na tabela de presença
// Função para lidar com registros de profissionais na tabela de presença
function entrada_saida_profissionais($ma, $pdo) {
    echo "Processando registro de profissional para MA: $ma\n";
    
    // Verifica se já existe um registro para o MA na tabela de registros de profissionais
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_presenca_profissionais WHERE ma = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$ma]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_presenca_profissionais SET data_hora_saida = NOW() WHERE ma = ? AND id_registro_profissional = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$ma, $registro['id_registro_profissional']]);
        echo "Horário de saída de profissional atualizado para data_hora_saida: " . date('Y-m-d H:i:s') . "\n";
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_presenca_profissionais (ma, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$ma]);
        echo "Nova entrada de profissional registrada data_hora_entrada: " . date('Y-m-d H:i:s') . "\n";
    }
}



// Função para lidar com registros de alunos na tabela de presença
function entrada_saida_alunos($ma_aluno, $pdo) {
    echo "Processando registro de aluno para MA: $ma_aluno\n";
    
    // Verifica se já existe um registro para o MA na tabela de registros de alunos
    $stmt_registro = $pdo->prepare("SELECT * FROM tb_registro_presenca_alunos WHERE ma_aluno = ? ORDER BY data_hora_entrada DESC LIMIT 1");
    $stmt_registro->execute([$ma_aluno]);
    $registro = $stmt_registro->fetch(PDO::FETCH_ASSOC);

    if ($registro && !$registro['data_hora_saida']) {
        // Atualiza o horário de saída para o registro existente
        $sql_update = "UPDATE tb_registro_presenca_alunos SET data_hora_saida = NOW() WHERE ma_aluno = ? AND id_registro_aluno = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$ma_aluno, $registro['id_registro_aluno']]);
        echo "Horário de saída de aluno atualizado para data_hora_saida: " . date('Y-m-d H:i:s') . "\n";
    } else {
        // Cria um novo registro de entrada
        $sql_insert = "INSERT INTO tb_registro_presenca_alunos (ma_aluno, data_hora_entrada) VALUES (?, NOW())";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$ma_aluno]);
        echo "Nova entrada de aluno registrada data_hora_entrada: " . date('Y-m-d H:i:s') . "\n";
    }
}



// Função para verificar se é o primeiro acesso do profissional/aluno e redirecionar conforme necessário
function existe_dados_vazios($ma, $pdo) {

    session_start();

    // Verificar na tabela de professores
    $sql_check_professor = "SELECT senha FROM tb_profissionais WHERE ma = :ma";
    $stmt_check_professor = $pdo->prepare($sql_check_professor);
    $stmt_check_professor->bindParam(':ma', $ma);
    $stmt_check_professor->execute();
    $result_professor = $stmt_check_professor->fetch(PDO::FETCH_ASSOC);

    if ($result_professor) { // Professor encontrado
        if (empty($result_professor['senha'])) { // Senha vazia, redirecionar para cadastro de nova senha
            $_SESSION['ma'] = $ma; // Salvar ma na sessão
            header("Location: primeiro_acesso_professor.php");
            exit;
        } else {
            header("Location: login.php?aviso=Não é seu primeiro acesso.");
            exit;
        }
    }

    // Verificar na tabela de alunos
    $sql_check_aluno = "SELECT senha FROM tb_alunos WHERE ma_aluno = :ma";
    $stmt_check_aluno = $pdo->prepare($sql_check_aluno);
    $stmt_check_aluno->bindParam(':ma', $ma);
    $stmt_check_aluno->execute();
    $result_aluno = $stmt_check_aluno->fetch(PDO::FETCH_ASSOC);

    if ($result_aluno) { // Aluno encontrado
        if (empty($result_aluno['senha'])) { // Senha vazia, redirecionar para cadastro de nova senha
            $_SESSION['ma'] = $ma; // Salvar ma na sessão
            header("Location: primeiro_acesso_aluno.php");
            exit;
        } else {
            header("Location: login.php?aviso=Não é seu primeiro acesso.");
            exit;
        }
    }

    // Se não encontrou nenhum registro para o MA em professores ou alunos
    header("Location: login.php");
    exit;
}

// Função para atualizar os dados do aluno no primeiro acesso
function primeiro_acesso_aluno($ma, $cod_genero, $telefone, $data_nascimento, $cpf, $email, $senha_md5, $pdo) {
    try {
        // Prepara a consulta SQL para atualizar os dados do aluno na tabela tb_alunos
        $sql = "UPDATE tb_alunos SET 
                cod_genero = :cod_genero, 
                telefone = :telefone, 
                data_nascimento = :data_nascimento, 
                cpf = :cpf, 
                email = :email, 
                senha = :senha 
                WHERE ma_aluno = :ma";
        $stmt = $pdo->prepare($sql);
        
        // Liga os parâmetros aos valores recebidos pela função
        $stmt->bindParam(':ma', $ma);
        $stmt->bindParam(':cod_genero', $cod_genero);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha_md5);
        
        // Executa a consulta SQL
        if ($stmt->execute()) {
            // Redireciona para uma página de sucesso
            header("Location: login.php?sucesso=Dados atualizados com sucesso.");
        } else {
            // Em caso de erro, redireciona de volta para o formulário com uma mensagem de erro
            header("Location: primeiro_acesso_aluno.php?ma=$ma&mensagem=Erro ao atualizar os dados. Por favor, tente novamente.");
        }
    } catch (PDOException $e) {
        // Em caso de exceção, redireciona de volta para o formulário com uma mensagem de erro
        header("Location: primeiro_acesso_aluno.php?ma=$ma&mensagem=Erro ao atualizar os dados: " . $e->getMessage());
    }
    exit;
}







// Função para definir a senha inicial do profissional no primeiro acesso
function primeiro_acesso_profissional($ma, $senha, $pdo) {//Funcionando
    
    $sql = "UPDATE tb_profissionais SET senha = :senha WHERE ma = :ma";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':senha', $senha);
    $stmt->bindParam(':ma', $ma);
    $stmt->execute();

    if ($stmt->execute()) {
        // Redireciona para uma página de sucesso
        header("Location: login.php?sucesso=Senha definida com sucesso.");
    } else {
        // Em caso de erro, redireciona de volta para o formulário com uma mensagem de erro
        header("Location: primeiro_acesso_professor.php?ma=$ma&mensagem=Erro ao definir a senha. Por favor, tente novamente.");
    }
    exit;
}

// Função para atualizar os dados do aluno no banco de dados
function acrescentar_professor($uid_rfid, $nome, $data_nasc, $cpf, $email, $telefone, $cod_genero, $cod_area, $pdo) {
    try {
        // Iniciar uma transação
        $pdo->beginTransaction();

        // Gerar novo MA
        $novo_ma = gerar_novo_ma($pdo);

        // Definir a consulta SQL para inserir um novo professor
        $sql_profissionais = "INSERT INTO tb_profissionais 
                              (ma, uid_rfid, nome, data_nascimento, cpf, email, telefone, cod_genero, cod_categoria, cod_status, data_registro) 
                              VALUES (:novo_ma, :uid_rfid, :nome, :data_nasc, :cpf, :email, :telefone, :cod_genero, :cod_categoria, :cod_status, NOW())";

        // Preparar os parâmetros para a consulta SQL
        $parameters_profissionais = array(
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

        // Preparar e executar a consulta SQL para tb_profissionais
        $stmt = $pdo->prepare($sql_profissionais);
        $stmt->execute($parameters_profissionais);

        // Definir a consulta SQL para inserir na tabela tb_professores_areas
        $sql_areas = "INSERT INTO tb_professores_areas (ma_prof, cod_area) VALUES (:novo_ma, :cod_area)";
        
        // Preparar os parâmetros para a consulta SQL
        $parameters_areas = array(
            ':novo_ma' => $novo_ma,
            ':cod_area' => $cod_area
        );

        // Preparar e executar a consulta SQL para tb_professores_areas
        $stmt = $pdo->prepare($sql_areas);
        $stmt->execute($parameters_areas);

        // Confirmar a transação
        $pdo->commit();
        
        // Redirecionar com mensagem de sucesso
        header("Location: cadastrar_novo_usuario.php?mensagem=Professor cadastrado com sucesso.");
        exit;

    } catch (Exception $e) {
        // Reverter a transação em caso de erro
        $pdo->rollBack();
        // Registrar ou exibir a mensagem de erro
        error_log($e->getMessage());
        header("Location: cadastrar_novo_usuario.php?mensagem=Erro ao cadastrar professor.");
        exit;
    }
}



// Função para adicionar um novo aluno ao banco de dados
function acrescentar_aluno($nome, $uid_rfid, $cod_categoria, $cod_curso, $pdo) {//Não testado
    // Gerar um novo MA (supondo que essa função exista e funcione corretamente)
    $novo_ma = gerar_novo_ma($pdo);

    // Definir a consulta SQL para inserir um novo aluno
    $sql = "INSERT INTO tb_alunos 
            (ma_aluno, uid_rfid, nome, cod_categoria, cod_curso, data_registro) 
            VALUES (:novo_ma, :uid_rfid, :nome, :cod_categoria, :cod_curso, NOW())";

    // Preparar os parâmetros para a consulta SQL
    $parameters = array(
        ':novo_ma' => $novo_ma,
        ':uid_rfid' => $uid_rfid,
        ':nome' => $nome,
        ':cod_categoria' => $cod_categoria, // Utiliza o código de categoria fornecido
        ':cod_curso' => $cod_curso // Utiliza o código de curso fornecido
    );

    // Preparar e executar a consulta SQL
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameters);

    // Redirecionar para a página inicial após a inserção
    header("Location: cadastrar_novo_usuario.php?mensagem=Aluno cadastrado com sucesso.");
    exit; // Encerrar o script após o redirecionamento
}

// Função para obter dados de um usuário (profissional ou aluno) baseado no MA
function obter_dados($ma, $pdo) {
    try {
        $dados_usuario = array();

        // Consulta na tabela de profissionais
        $sql_profissional = "SELECT * FROM tb_profissionais WHERE ma = :ma";
        $stmt_profissional = $pdo->prepare($sql_profissional);
        $stmt_profissional->bindParam(':ma', $ma); // Vincula o parâmetro MA à consulta
        $stmt_profissional->execute();

        // Verifica se encontrou algum profissional com o MA fornecido
        if ($row = $stmt_profissional->fetch(PDO::FETCH_ASSOC)) {
            $dados_usuario = $row;
            return $dados_usuario; // Retorna os dados do profissional
        }

        // Consulta na tabela de alunos
        $sql_aluno = "SELECT * FROM tb_alunos WHERE ma_aluno = :ma";
        $stmt_aluno = $pdo->prepare($sql_aluno);
        $stmt_aluno->bindParam(':ma', $ma); // Vincula o parâmetro MA à consulta
        $stmt_aluno->execute();

        // Verifica se encontrou algum aluno com o MA fornecido
        if ($row = $stmt_aluno->fetch(PDO::FETCH_ASSOC)) {
            $dados_usuario = $row;
            return $dados_usuario; // Retorna os dados do aluno
        }

        // Se não encontrar em ambas as tabelas, retorna null
        return null;

    } catch (PDOException $e) {
        // Em caso de erro, tratar a exceção aqui
        echo "Erro ao obter dados do usuário: " . $e->getMessage();
        return null; // Retorna null em caso de exceção
    }
}

// Função para obter o gênero de um usuário com base no código de gênero
function obter_genero($cod_genero, $pdo) {
    // Consulta na tabela de generos
    $sql_genero = "SELECT genero FROM tb_generos WHERE cod_genero = :cod_genero";
    $stmt_genero = $pdo->prepare($sql_genero);
    $stmt_genero->bindParam(':cod_genero', $cod_genero, PDO::PARAM_INT); // Especifica o tipo de dado do parâmetro
    $stmt_genero->execute();

    // Verifica se algum resultado foi encontrado
    if ($row = $stmt_genero->fetch(PDO::FETCH_ASSOC)) {
        return $row['genero']; // Retorna o valor do gênero
    } else {
        return 'Humano'; // Retorna 'Humano' se nenhum gênero foi encontrado
    }
}


/**Obter o cargo com a variação de acordo com o gênero
 * @param int $cod_categoria - Código da categoria para buscar no banco de dados.
 * @param int $cod_genero - Código do gênero (1 para masculino, 2 para feminino).
 * @param PDO $pdo - Objeto de conexão com o banco de dados.
 */
function obter_cargo($cod_categoria, $cod_genero, $pdo) {
    

    // Consulta na tabela de categorias
    $sql_cargo = "SELECT categoria FROM tb_categoria WHERE cod_categoria = :cod_categoria";
    $stmt_cargo = $pdo->prepare($sql_cargo);
    // Associa o parâmetro cod_categoria à query SQL, especificando que é um inteiro
    $stmt_cargo->bindParam(':cod_categoria', $cod_categoria, PDO::PARAM_INT); 
    // Executa a query SQL
    $stmt_cargo->execute();

    // Verifica se algum resultado foi encontrado
    if ($row = $stmt_cargo->fetch(PDO::FETCH_ASSOC)) {
        $categoria = $row['categoria'];

        // Ajusta a categoria com base no gênero
        if ($cod_genero == 1) { // Código para masculino
            return $categoria;
        } elseif ($cod_genero == 2) { // Código para feminino
            // Altera a categoria para a forma feminina se aplicável
            if ($categoria == 'Aluno'){
                return 'Aluna';
            } elseif ($categoria == 'Professor') {
                return 'Professora';
            } elseif ($categoria == 'Coordenador') {
                return 'Coordenadora';
            } else {
                return $categoria;
            }
        } else {
            // Retorna a categoria original se o gênero não for especificado
            return $categoria;
        }
    } else {
        // Retorna se nenhuma categoria foi encontrada
        return 'Só Deus sabe o cargo';
    }
}








/**
 * Faz o upload de uma imagem associada ao usuário (aluno ou profissional).
 * 
 * @param string $ma O código identificador do usuário.
 * @param string $tipo O tipo de usuário ('1' para aluno, qualquer outro valor para profissional).
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados.
 * @return bool Retorna true se o upload foi bem-sucedido, false caso contrário.
 */
function upload_imagem($ma, $tipo, $pdo){

    // Verifica se já existe uma imagem associada ao usuário
    $existe = checar_imagem_existe($ma, $tipo, $pdo);

    // Se a imagem já existe, atualiza a imagem
    if($existe) {
        return atualizar_imagem($ma, $tipo, $pdo);
    } else {
        // Se não existe imagem, realiza o upload de uma nova imagem
        if(isset($_FILES['arquivo'])){
            $extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION)); // Obtém a extensão do arquivo
            $extensoes_permitidas = array('jpg', 'jpeg', 'png', 'gif'); // Extensões permitidas
            
            // Verifica se a extensão do arquivo é permitida
            if(in_array($extensao, $extensoes_permitidas)){
                $novo_nome = md5(time()) . '.' . $extensao; // Gera um novo nome para o arquivo usando hash md5
                $diretorio = "upload/"; // Diretório para upload das imagens

                // Caminho completo da imagem original
                $imagem_tmp = $_FILES['arquivo']['tmp_name'];

                // Move o arquivo para o diretório especificado
                if(move_uploaded_file($imagem_tmp, $diretorio . $novo_nome)){
                    $data_upload = date("Y-m-d H:i:s"); // Obtém a data e hora do upload
                    $nome_imagem = $diretorio . $novo_nome; // Caminho completo da imagem

                    // Prepara a query SQL para inserir a imagem no banco de dados
                    if ($tipo == '1') {
                        $stmt = $pdo->prepare("INSERT INTO tb_aluno_foto (ma_aluno, nome_imagem, data_upload) VALUES (:ma, :nome_imagem, :data_upload)");
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO tb_profissional_foto (ma_profissional, nome_imagem, data_upload) VALUES (:ma, :nome_imagem, :data_upload)");
                    }

                    $stmt->bindParam(':ma', $ma);
                    $stmt->bindParam(':nome_imagem', $nome_imagem);
                    $stmt->bindParam(':data_upload', $data_upload);

                    // Executa a query e redireciona para a página de perfil
                    if($stmt->execute()){
                        header("Location: profile.php");
                        return true;
                    } else {
                        return false; // Falha ao inserir a imagem no banco de dados
                    }
                } else {
                    return false; // Falha ao mover o arquivo
                }
            } else {
                return false; // Extensão do arquivo não permitida
            }
        } else {
            return false; // Nenhum arquivo foi enviado
        }
    }
}





/**
 * Atualiza a imagem associada ao usuário (aluno ou profissional).
 * 
 * @param string $ma O código identificador do usuário.
 * @param string $tipo O tipo de usuário ('1' para aluno, qualquer outro valor para profissional).
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados.
 * @return bool Retorna true se a atualização foi bem-sucedida, false caso contrário.
 */

function atualizar_imagem($ma, $tipo, $pdo){

    // Verifica se um arquivo foi enviado no formulário
    if(isset($_FILES['arquivo'])){
        $extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION)); // Obtém a extensão do arquivo
        $extensoes_permitidas = array('jpg', 'jpeg', 'png', 'gif'); // Extensões permitidas
        
        // Verifica se a extensão do arquivo é permitida
        if(in_array($extensao, $extensoes_permitidas)){
            $novo_nome = md5(time()) . '.' . $extensao; // Gera um novo nome para o arquivo usando hash md5
            $diretorio = "upload/"; // Diretório para upload das imagens

            // Move o arquivo para o diretório especificado
            if(move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.$novo_nome)){
                $data_upload = date("Y-m-d H:i:s"); // Obtém a data e hora do upload
                $nome_imagem = $diretorio . $novo_nome; // Caminho completo da imagem

                try {
                    // Prepara a query SQL para atualizar a imagem no banco de dados
                    if ($tipo == '1') {
                        $stmt = $pdo->prepare("UPDATE tb_aluno_foto SET nome_imagem = :nome_imagem, data_upload = :data_upload WHERE ma_aluno = :ma");
                        $stmt->bindParam(':ma', $ma);
                    } else {
                        $stmt = $pdo->prepare("UPDATE tb_profissional_foto SET nome_imagem = :nome_imagem, data_upload = :data_upload WHERE ma_profissional = :ma");
                        $stmt->bindParam(':ma', $ma);
                    }

                    $stmt->bindParam(':nome_imagem', $nome_imagem);
                    $stmt->bindParam(':data_upload', $data_upload);

                    // Executa a query e redireciona para a página de perfil
                    if($stmt->execute()){
                        header("Location: profile.php");
                        return true; // Atualização bem-sucedida
                    } else {
                        return false; // Falha ao executar a query
                    }
                } catch(PDOException $e) {
                    // Poderia logar o erro ou retornar uma mensagem específica
                    return false; // Erro ao preparar ou executar a query
                }
            } else {
                return false; // Falha ao mover o arquivo para o diretório
            }
        } else {
            return false; // Extensão do arquivo não permitida
        }
    } else {
        return false; // Nenhum arquivo foi enviado
    }
}



/**
 * Verifica se existe uma imagem associada ao usuário (aluno ou profissional) e a exclui se existir.
 * 
 * @param string $ma O código identificador do usuário.
 * @param string $tipo O tipo de usuário ('1' para aluno, qualquer outro valor para profissional).
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados.
 * @return bool Retorna true se a imagem foi excluída com sucesso, false caso contrário.
 */
function checar_imagem_existe($ma, $tipo, $pdo){

    // Prepara a query SQL com base no tipo de usuário
    if ($tipo == '1') {
        // Prepara a consulta para verificar na tabela de fotos dos alunos
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM tb_aluno_foto WHERE ma_aluno = :ma");
    } else {
        // Prepara a consulta para verificar na tabela de fotos dos profissionais
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM tb_profissional_foto WHERE ma_profissional = :ma");
    }
    
    // Vincula o parâmetro ma à consulta
    $stmt->bindParam(':ma', $ma);
    // Executa a consulta
    $stmt->execute();
    
    // Obtém o resultado da consulta
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verifica se existe alguma imagem associada ao usuário
    if ($resultado && $resultado['total'] > 0) {
        return true; // Já existe uma imagem associada ao ma
    } else {
        return false; // Não existe imagem associada ao ma
    }
}



/**
 * Obtém o caminho da imagem associada ao usuário (aluno ou profissional).
 * 
 * @param string $ma O código identificador do usuário.
 * @param string $tipo O tipo de usuário ('1' para aluno, qualquer outro valor para profissional).
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados.
 * @return string Retorna o caminho completo da imagem se encontrada, ou o caminho para uma imagem padrão caso contrário.
 */
function obter_caminho_imagem($ma, $tipo, $pdo){

    // Prepara e executa a consulta SQL para obter o nome da imagem associada ao usuário
    if ($tipo == '1') {
        // Busca na tabela de alunos
        $stmt = $pdo->prepare("SELECT nome_imagem FROM tb_aluno_foto WHERE ma_aluno = :ma");
    } else {
        // Busca na tabela de profissionais
        $stmt = $pdo->prepare("SELECT nome_imagem FROM tb_profissional_foto WHERE ma_profissional = :ma");
    }

    $stmt->bindParam(':ma', $ma);
    $stmt->execute();
    
    // Obtém o resultado da consulta
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Se encontrou o nome da imagem, retorna o caminho completo
    if ($resultado) {
        return $resultado['nome_imagem']; // Retorna o caminho da imagem
    } else {
        $imagem_padrao = 'vendors/images/photo1.jpg'; // Caminho para imagem padrão caso não encontre
        return $imagem_padrao; // Retorna falso se não encontrar a imagem
    }
}

/**
 * Obtém as disciplinas associadas a um aluno ou profissional com base no código identificador.
 * 
 * @param string $ma O código identificador do usuário.
 * @param string $tipo O tipo de usuário ('1' para aluno, qualquer outro valor para profissional).
 * @param PDO $pdo Objeto PDO para conexão com o banco de dados.
 * @return array Retorna um array associativo com os IDs das disciplinas como chave e os nomes das disciplinas como valor.
 */
function obter_disciplinas($ma, $tipo, $pdo){
    // Inicializa o array para armazenar os resultados
    $disciplinas = array();

    // Verifica o tipo de usuário para decidir a tabela a ser consultada
    if ($tipo == '1') {
        // Busca na tabela de alunos
        $sql = "SELECT id_grade_horaria FROM tb_alunos_aulas WHERE ma_aluno = :ma";
    } else {
        // Busca na tabela de profissionais
        $sql = "SELECT id_grade_horaria FROM tb_profissionais_aulas WHERE ma_prof = :ma";
    }

    // Prepara e executa a consulta para obter os IDs de grade horária
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':ma', $ma);
    $stmt->execute();

    // Obtém todos os IDs de grade horária associados ao usuário
    $ids_grade_horaria = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Verifica se foram encontrados IDs de grade horária
    if ($ids_grade_horaria) {
        // Constrói a string de placeholders para IN clause
        $placeholders = implode(',', array_fill(0, count($ids_grade_horaria), '?'));

        // Consulta SQL para buscar os nomes das disciplinas pelas grades horárias encontradas
        $sql_nome_disciplinas = "SELECT DISTINCT d.nome 
                                 FROM tb_grade_horaria gh
                                 JOIN tb_disciplinas d ON gh.id_disciplina = d.id_disciplina
                                 WHERE gh.id_grade_horaria IN ($placeholders)";
        
        $stmt_nome_disciplinas = $pdo->prepare($sql_nome_disciplinas);
        $stmt_nome_disciplinas->execute($ids_grade_horaria);

        // Obtém os nomes das disciplinas
        $resultados_disciplinas = $stmt_nome_disciplinas->fetchAll(PDO::FETCH_COLUMN);

        // Adiciona os nomes das disciplinas ao array
        $disciplinas = $resultados_disciplinas;
    }

    // Retorna o array de disciplinas
    return $disciplinas;
}


function contar_dados_index($ma, $tipo, $op, $pdo){
    if($tipo == 1) {
        // Operações para alunos
        switch($op){
            case 1: // Qtd de disciplinas que o aluno está matriculado
                $stmt = $pdo->prepare("SELECT COUNT(id_grade_horaria) AS qtd_disciplinas FROM tb_alunos_aulas WHERE ma_aluno = :ma");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['qtd_disciplinas'];

            case 2: // Média das notas do aluno
                $stmt = $pdo->prepare("SELECT AVG(nota) AS media_notas FROM tb_nota_aluno WHERE ma_aluno = :ma");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['media_notas'];

            case 3: // Qtd de presenças do aluno
                $stmt = $pdo->prepare("SELECT COUNT(id_registro_aluno) AS qtd_presencas FROM tb_registro_presenca_alunos WHERE ma_aluno = :ma");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['qtd_presencas'];
        }
    } elseif($tipo == 2) {
        // Operações para professores
        switch($op){
            case 1: // Qtd de disciplinas que o professor leciona
                $stmt = $pdo->prepare("SELECT COUNT(id_grade_horaria) AS qtd_disciplinas FROM tb_profissionais_aulas WHERE ma_prof = :ma");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['qtd_disciplinas'];

            case 2: // Pegar o nome da área de ensino
                $stmt = $pdo->prepare("
                    SELECT a.area 
                    FROM tb_professores_areas pa
                    INNER JOIN tb_areas a ON pa.cod_area = a.cod_area
                    WHERE pa.ma_prof = :ma
                ");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['area'];

            case 3: // Diferença de dias entre o registro e a data atual
                $stmt = $pdo->prepare("
                    SELECT DATEDIFF(NOW(), data_registro) AS dias_diff
                    FROM tb_profissionais 
                    WHERE ma = :ma
                ");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['dias_diff'];

            case 4: // Qtd de ocorrências para o professor
                $stmt = $pdo->prepare("SELECT COUNT(id_ocorrencia) AS qtd_ocorrencias FROM tb_ocorrencias_profissionais WHERE ma_prof = :ma");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['qtd_ocorrencias'];
        }
    } else {
        // Operações para coordenadores
        switch($op){
            case 1: // Pegar o nome da área de ensino supervisionada
                $stmt = $pdo->prepare("
                    SELECT a.area 
                    FROM tb_coordenacao_area ca
                    INNER JOIN tb_areas a ON ca.cod_area = a.cod_area
                    WHERE ca.ma_coordenacao = :ma
                ");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['area'];

            case 2: // Qtd de professores que o coordenador supervisiona
                $stmt = $pdo->prepare("
                    SELECT COUNT(pa.id_prof_area) AS qtd_professores
                    FROM tb_professores_areas pa
                    INNER JOIN tb_coordenacao_area ca ON pa.cod_area = ca.cod_area
                    WHERE ca.ma_coordenacao = :ma
                ");
                $stmt->bindParam(':ma', $ma, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['qtd_professores'];

            case 3: // Qtd de coordenadores
                $stmt = $pdo->prepare("SELECT COUNT(cod_categoria) AS qtd_coordenadores FROM tb_profissionais WHERE cod_categoria = 2");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['qtd_coordenadores'];

            case 4: // Diferença de dias entre o registro e a data atual
                $stmt = $pdo->prepare("
                    SELECT DATEDIFF(NOW(), data_registro) AS dias_diff
                    FROM tb_profissionais 
                    WHERE ma = :ma
                ");
                $stmt->bindParam(':ma', $ma);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['dias_diff'];
        }
    }
    
    return 0;
}

function listar_graduacoes($pdo){

    // Query para obter os cursos de graduação
    $stmt = $pdo->query("SELECT cod_graduacao, graduacao FROM tb_graduacoes");
    $graduacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $graduacoes;
}
function listar_areas($pdo){

    // Query para obter os cursos de graduação
    $stmt = $pdo->query("SELECT cod_area, area FROM tb_areas");
    $graduacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $graduacoes;
}

function listar_alunos($pdo){

    $stmt = $pdo->query("
        SELECT a.ma_aluno, a.uid_rfid, a.nome, a.data_registro , g.graduacao AS curso
        FROM tb_alunos a
        JOIN tb_graduacoes g ON a.cod_curso = g.cod_graduacao
    ");
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $alunos;
}

function listar_professores($pdo){
    $stmt = $pdo->query("SELECT ma, uid_rfid, nome, data_registro FROM tb_profissionais WHERE cod_categoria = 2
    ");
    $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $professores;
}



function listar_coordenadores($pdo){

    $stmt = $pdo->query("SELECT ma, uid_rfid, nome, data_registro FROM tb_profissionais WHERE cod_categoria = 3
    ");
    $coordenadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $coordenadores;
}

function listar_registros($tipo ,$pdo){
    
    if($tipo == 1)
    {
        $stmt = $pdo->query("SELECT r.ma_aluno, a.nome, cds.dia_semana, r.data_hora_entrada, r.data_hora_saida
                            FROM tb_registro_presenca_alunos r
                            JOIN tb_alunos a ON r.ma_aluno = a.ma_aluno
                            JOIN tb_cod_dia_semana cds ON cds.cod_dia_semana = DAYOFWEEK(r.data_hora_entrada);
                            ");
        $registros_alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($registros_alunos as &$registro) {
            $dataHoraEntrada = new DateTime($registro['data_hora_entrada']);
            $registro['data'] = $dataHoraEntrada->format('d/m/Y');
            $registro['hora_entrada'] = $dataHoraEntrada->format('H:i:s');
    
            if ($registro['data_hora_saida']) {
                $dataHoraSaida = new DateTime($registro['data_hora_saida']);
                $registro['hora_saida'] = $dataHoraSaida->format('H:i:s');
            } else {
                $registro['hora_saida'] = 'N/A';
                $registro['duracao'] = 'N/A';
                    }
        }

        return $registros_alunos;
    }
    else
    {
        $stmt = $pdo->query("SELECT r.ma, p.nome, cds.dia_semana, r.data_hora_entrada, r.data_hora_saida
                            FROM tb_registro_presenca_profissionais r
                            JOIN tb_profissionais p ON r.ma = p.ma
                            JOIN tb_cod_dia_semana cds ON cds.cod_dia_semana = DAYOFWEEK(r.data_hora_entrada);
                            ");
        $registros_professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($registros_professores as &$registro) {
            $dataHoraEntrada = new DateTime($registro['data_hora_entrada']);
            $registro['data'] = $dataHoraEntrada->format('d/m/Y');
            $registro['hora_entrada'] = $dataHoraEntrada->format('H:i:s');
    
            if ($registro['data_hora_saida']) {
                $dataHoraSaida = new DateTime($registro['data_hora_saida']);
                $registro['hora_saida'] = $dataHoraSaida->format('H:i:s');
            } else {
                $registro['hora_saida'] = 'N/A';
                $registro['duracao'] = 'N/A';
                    }
        }

        return $registros_professores;
    }
}


function listar_ocorrencias($tipo ,$pdo){
    
    if($tipo == 1)
    {
        $stmt = $pdo->query("SELECT oa.ma_aluno,
                                    a.nome AS nome_aluno,
                                    cds.dia_semana,
                                    oa.data_hora_ocorrencia,
                                    tpo.ocorrencia,
                                    oa.id_grade_horaria,
                                    d.nome AS disciplina

                            FROM tb_ocorrencias_alunos oa
                            JOIN tb_alunos a ON a.ma_aluno = oa.ma_aluno
                            JOIN tb_tipo_ocorrencia tpo ON tpo.id_tipo_ocorrencia = oa.id_tipo_ocorrencia
                            JOIN tb_grade_horaria gd ON gd.id_grade_horaria = oa.id_grade_horaria
                            JOIN tb_disciplinas d ON d.id_disciplina = gd.id_disciplina
                            JOIN tb_cod_dia_semana cds ON cds.cod_dia_semana = DAYOFWEEK(oa.data_hora_ocorrencia);
                            ");

        $ocorrencias_alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ocorrencias_alunos as &$registro) {
            $dataHoraOcorrencia = new DateTime($registro['data_hora_ocorrencia']);
            $registro['data_ocorrencia'] = $dataHoraOcorrencia->format('d/m/Y');
            $registro['hora_ocorrencia'] = $dataHoraOcorrencia->format('H:i:s');
        }

        return $ocorrencias_alunos;
    }
    else
    {
        $stmt = $pdo->query("SELECT op.ma_profissional,
                                    p.nome AS nome_profissional,
                                    cds.dia_semana,
                                    op.data_hora_ocorrencia,
                                    tpo.ocorrencia,
                                    op.id_grade_horaria,
                                    d.nome AS disciplina

                            FROM tb_ocorrencias_profissionais op
                            JOIN tb_profissionais p ON p.ma = op.ma_profissional
                            JOIN tb_tipo_ocorrencia tpo ON tpo.id_tipo_ocorrencia = op.id_tipo_ocorrencia
                            JOIN tb_grade_horaria gd ON gd.id_grade_horaria = op.id_grade_horaria
                            JOIN tb_disciplinas d ON d.id_disciplina = gd.id_disciplina
                            JOIN tb_cod_dia_semana cds ON cds.cod_dia_semana = DAYOFWEEK(op.data_hora_ocorrencia);
                            ");

        $ocorrencias_profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ocorrencias_profissionais as &$registro) {
            $dataHoraOcorrencia = new DateTime($registro['data_hora_ocorrencia']);
            $registro['data_ocorrencia'] = $dataHoraOcorrencia->format('d/m/Y');
            $registro['hora_ocorrencia'] = $dataHoraOcorrencia->format('H:i:s');
        }

        return $ocorrencias_profissionais;

    }
}


function obter_grade_horaria($ma, $categoria, $pdo) {
    if ($categoria == 1) {
        $stmt = $pdo->prepare("
            SELECT gh.id_grade_horaria,
                   d.nome AS disciplina,
                   cds.dia_semana,
                   gh.hora_inicio,
                   gh.hora_fim,
                   gh.sala
            FROM tb_grade_horaria gh
            JOIN tb_alunos_aulas aa ON aa.id_grade_horaria = gh.id_grade_horaria AND aa.ma_aluno = :ma
            JOIN tb_disciplinas d ON d.id_disciplina = gh.id_disciplina
            JOIN tb_cod_dia_semana cds ON cds.cod_dia_semana = gh.cod_dia_semana
            ORDER BY cds.dia_semana ASC
        ");
        
        $stmt->execute([':ma' => $ma]);
        
        $agenda = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($agenda as &$registro) {
            // Formatando hora de início e fim, exemplo:
            $registro['hora_inicio'] = date('H:i', strtotime($registro['hora_inicio']));
            $registro['hora_fim'] = date('H:i', strtotime($registro['hora_fim']));
        }
        
        return $agenda;
    } else {
        $stmt = $pdo->prepare("
            SELECT gh.id_grade_horaria,
                   d.nome AS disciplina,
                   cds.dia_semana,
                   gh.hora_inicio,
                   gh.hora_fim,
                   gh.sala
            FROM tb_grade_horaria gh
            JOIN tb_profissionais_aulas pa ON pa.id_grade_horaria = gh.id_grade_horaria AND pa.ma_prof = :ma
            JOIN tb_disciplinas d ON d.id_disciplina = gh.id_disciplina
            JOIN tb_cod_dia_semana cds ON cds.cod_dia_semana = gh.cod_dia_semana
        ");
        
        $stmt->execute([':ma' => $ma]);
        
        $agenda = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($agenda as &$registro) {
            // Formatando hora de início e fim, exemplo:
            $registro['hora_inicio'] = date('H:i', strtotime($registro['hora_inicio']));
            $registro['hora_fim'] = date('H:i', strtotime($registro['hora_fim']));
        }
        
        return $agenda;
    }
}
