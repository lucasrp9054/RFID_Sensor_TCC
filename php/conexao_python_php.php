<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "functions.php";
include "acesso_bd.php"; // Certifique-se de que o acesso ao banco de dados está configurado corretamente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'];

    // Verifica e lida com o registro
    checkAndHandleRegistration($uid, $pdo);
} else {
    echo "Acesso ao script está funcionando.";
}
?>
