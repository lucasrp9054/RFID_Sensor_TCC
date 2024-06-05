<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=db_sistema_tcc", "lucasr", "r9054");
    // Define o modo de erro do PDO para exceção
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}


