<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include 'funcoes/geral/utilidades.php';
    include 'bd/acesso_bd.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $uid = $_POST['uid'];

        //Passa o uid para checagem no banco de dados
        checkAndHandleRegistration($uid, $pdo);
    }
    else
    {
        echo "Acesso ao script está funcionando.";
    }

