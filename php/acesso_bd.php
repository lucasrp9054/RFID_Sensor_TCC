<?php

// Configurações do banco de dados
$host = 'localhost'; // Host do banco de dados
$dbname = 'db_sistema_tcc'; // Nome do banco de dados
$username = 'lucasr'; // Nome de usuário do banco de dados
$password = 'r9054'; // Senha do banco de dados

try {
    // Conexão com o banco de dados usando PDO
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    
    // Configuração de erros para lançar exceções em caso de problemas
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configuração para usar caracteres UTF-8
    $pdo->exec("set names utf8");
    
} catch(PDOException $e) {
    // Em caso de erro na conexão, exibe mensagem de erro
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
