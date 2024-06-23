<?php

    include "acesso_bd.php";
    include "functions.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $uid = $_POST['uid'];

        // Passa o uid para checagem no banco de dados
        verificar_e_tratar_registro($uid, $pdo);
    } else {
        echo "Acesso ao script está funcionando.";
    }

