# Projeto RFID Sensor TCC

Este projeto consiste em um sistema de leitura de tags RFID utilizando Arduino e Python para interação com um banco de dados MySQL via PHP.

## Pré-requisitos

Antes de iniciar, certifique-se de ter instalado:

- *XAMPP*: Para configurar um servidor web local com PHP e MySQL.
- *Python*: Para executar o script de leitura do sensor RFID.
- *Arduino IDE*: Para programar o Arduino.

## Configuração do Ambiente

1. *Configuração do Banco de Dados*:
   - Importe o arquivo banco.sql no phpMyAdmin para criar o banco de dados db_sistema_tcc com as tabelas necessárias.

2. *Configuração do XAMPP*:
   - Inicie o XAMPP e verifique se os serviços Apache e MySQL estão ativos.
   - Coloque os arquivos PHP na pasta htdocs do XAMPP.

3. *Configuração do Arduino*:
   - Conecte o sensor RFID ao Arduino conforme o esquemático.
   - Carregue o código arduino_rfid_reader.ino no Arduino utilizando a Arduino IDE.

4. *Configuração do Python*:
   - Abra um terminal ou prompt de comando.
   - Navegue até a pasta onde está localizado o script Python:
     
     cd C:\xampp\htdocs\rfid_sensor_tcc_2\python
     
   - Execute o script Python para iniciar a leitura do sensor RFID:
     
     python sensor_rfid.py
     

## Funcionamento

- O script Python sensor_rfid.py lê os dados do sensor RFID conectado ao Arduino e envia os dados para o servidor local via HTTP POST.
- Os scripts PHP recebem os dados, consultam o banco de dados e registram a entrada ou saída do usuário identificado pela tag RFID.

## Estrutura de Arquivos

- arduino_rfid_reader.ino: Código Arduino para ler tags RFID e enviar dados para o serial.
- sensor_rfid.py: Script Python para ler dados do serial (Arduino) e enviar para o servidor via HTTP POST.
- conexao_python_php.php: Script PHP para receber os dados do Python e interagir com o banco de dados.
- acesso_bd.php: Arquivo PHP para configurar a conexão com o banco de dados.
- banco.sql: Arquivo SQL para criar o banco de dados e tabelas necessárias.

## Contribuição

- Para contribuir com melhorias, abra uma issue ou envie um pull request.


## Nova tabela

CREATE TABLE tb_ocorrencias_alunos (
    id_ocorrencia_aluno INT AUTO_INCREMENT PRIMARY KEY,
    ma_aluno VARCHAR(8),
    data_hora_ocorrencia TIMESTAMP NOT NULL,
    id_grade_horaria INT,
    id_tipo_ocorrencia INT NOT NULL,
    FOREIGN KEY (ma_aluno) REFERENCES tb_alunos(ma_aluno) ON DELETE CASCADE,
    FOREIGN KEY (id_grade_horaria) REFERENCES tb_grade_horaria(id_grade_horaria),
    FOREIGN KEY (id_tipo_ocorrencia) REFERENCES tb_tipo_ocorrencia(id_tipo_ocorrencia)
);

## INSERT
DELIMITER //

CREATE TRIGGER after_insert_tb_registro_presenca_alunos
AFTER INSERT ON tb_registro_presenca_alunos
FOR EACH ROW
BEGIN
    DECLARE ma_registro_aluno VARCHAR(8);
    DECLARE hora_entrada_real TIMESTAMP;
    DECLARE dia_semana_atual INT;
    DECLARE ids_aulas TEXT;
    DECLARE id_grade INT;
    DECLARE hora_inicio_aula TIME;
    DECLARE hora_fim_aula TIME;
    
    -- Capturar os valores atualizados
    SET ma_registro_aluno = NEW.ma_aluno;
    SET hora_entrada_real = NEW.data_hora_entrada;

    -- Obter dia da semana atual (1 = domingo, ..., 7 = sábado)
    SET dia_semana_atual = DAYOFWEEK(hora_entrada_real);

    -- Obter IDs das aulas do aluno
    SELECT COALESCE(GROUP_CONCAT(id_grade_horaria), '') INTO ids_aulas
    FROM tb_alunos_aulas
    WHERE ma_aluno = ma_registro_aluno;

    -- Verificar e registrar ocorrência para cada id_grade_horaria
    IF ids_aulas IS NOT NULL AND ids_aulas != '' THEN
        SET ids_aulas = CONCAT(ids_aulas, ','); -- Adicionar vírgula ao final para facilitar a busca

        -- Iterar sobre os IDs das aulas
        grade_loop: LOOP
            SET id_grade = SUBSTRING_INDEX(ids_aulas, ',', 1);

            -- Saída do loop se não houver mais IDs
            IF id_grade = '' THEN
                LEAVE grade_loop;
            END IF;

            -- Obter hora de início e de encerramento da aula
            SELECT hora_inicio, hora_fim INTO hora_inicio_aula, hora_fim_aula
            FROM tb_grade_horaria
            WHERE id_grade_horaria = id_grade AND cod_dia_semana = dia_semana_atual;

            -- Comparar hora de saída com hora de encerramento da aula
            IF hora_inicio_aula IS NOT NULL AND hora_fim_aula IS NOT NULL AND hora_entrada_real > hora_inicio_aula AND hora_entrada_real < hora_fim_aula THEN

                -- Inserir ocorrência na tb_ocorrencias_alunos
                INSERT INTO tb_ocorrencias_alunos (ma_aluno, data_hora_ocorrencia, id_grade_horaria, id_tipo_ocorrencia)
                VALUES (ma_registro_aluno, hora_entrada_real, id_grade, 2); -- id_tipo_ocorrencia = 2 para entrada fora do horário


                -- Saída do loop após a primeira ocorrência válida
                LEAVE grade_loop;
            END IF;

            -- Remover o ID processado da lista
            SET ids_aulas = SUBSTRING(ids_aulas, LENGTH(id_grade) + 2);
        END LOOP;
    END IF;
END;
//

DELIMITER ;


## Update
DELIMITER //

CREATE TRIGGER after_update_tb_registro_presenca_alunos
AFTER UPDATE ON tb_registro_presenca_alunos
FOR EACH ROW
BEGIN
    DECLARE ma_registro_aluno VARCHAR(8);
    DECLARE hora_saida_real TIMESTAMP;
    DECLARE dia_semana_atual INT;
    DECLARE ids_aulas TEXT;
    DECLARE id_grade INT;
    DECLARE hora_inicio_aula TIME;
    DECLARE hora_fim_aula TIME;
    
    -- Capturar os valores atualizados
    SET ma_registro_aluno = NEW.ma_aluno;
    SET hora_saida_real = NEW.data_hora_saida;

    -- Obter dia da semana atual (1 = domingo, ..., 7 = sábado)
    SET dia_semana_atual = DAYOFWEEK(hora_saida_real);

    -- Obter IDs das aulas do aluno
    SELECT COALESCE(GROUP_CONCAT(id_grade_horaria), '') INTO ids_aulas
    FROM tb_alunos_aulas
    WHERE ma_aluno = ma_registro_aluno;

    -- Verificar e registrar ocorrência para cada id_grade_horaria
    IF ids_aulas IS NOT NULL AND ids_aulas != '' THEN
        SET ids_aulas = CONCAT(ids_aulas, ','); -- Adicionar vírgula ao final para facilitar a busca

        -- Iterar sobre os IDs das aulas
        grade_loop: LOOP
            SET id_grade = SUBSTRING_INDEX(ids_aulas, ',', 1);

            -- Saída do loop se não houver mais IDs
            IF id_grade = '' THEN
                LEAVE grade_loop;
            END IF;

            -- Obter hora de início e de encerramento da aula
            SELECT hora_inicio, hora_fim INTO hora_inicio_aula, hora_fim_aula
            FROM tb_grade_horaria
            WHERE id_grade_horaria = id_grade AND cod_dia_semana = dia_semana_atual;

            -- Comparar hora de saída com hora de encerramento da aula
            IF hora_fim_aula IS NOT NULL AND hora_inicio_aula IS NOT NULL AND hora_saida_real > hora_inicio_aula AND hora_saida_real < hora_fim_aula THEN
                -- Inserir ocorrência na tb_ocorrencias_alunos
                INSERT INTO tb_ocorrencias_alunos (ma_aluno, data_hora_ocorrencia, id_grade_horaria, id_tipo_ocorrencia)
                VALUES (ma_registro_aluno, hora_saida_real, id_grade, 3); -- id_tipo_ocorrencia = 3 para saída antecipada do horário

                -- Saída do loop após a primeira ocorrência válida
                LEAVE grade_loop;
            END IF;

            -- Remover o ID processado da lista
            SET ids_aulas = SUBSTRING(ids_aulas, LENGTH(id_grade) + 2);
        END LOOP;
    END IF;
END;
//

DELIMITER ;