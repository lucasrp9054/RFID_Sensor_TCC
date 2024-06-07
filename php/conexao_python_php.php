<?php
include "functions.php";
include "acesso_bd.php"; // Certifique-se de que o acesso ao banco de dados está configurado corretamente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'];

    // Verifica e lida com o registro
    checkAndHandleRegistration($uid, $pdo);
    
}

