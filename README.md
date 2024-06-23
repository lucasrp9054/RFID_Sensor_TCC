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


Novas tabelas

CREATE TABLE tb_aluno_foto (
id_aluno_foto INT AUTO_INCREMENT PRIMARY KEY,
ma_aluno VARCHAR(8) NOT NULL,
nome_imagem VARCHAR(50) NOT NULL,
data_upload DATETIME NOT NULL,
FOREIGN KEY (ma_aluno) REFERENCES tb_alunos(ma_aluno)
);

CREATE TABLE tb_profissional_foto (
id_profissional_foto INT AUTO_INCREMENT PRIMARY KEY,
ma_profissional VARCHAR(8) NOT NULL,
nome_imagem VARCHAR(50) NOT NULL,
data_upload DATETIME NOT NULL,
FOREIGN KEY (ma_profissional) REFERENCES tb_profissionais(ma)
);