<?php

include "functions.php";

// Verifica se o UID do cartão foi recebido
if (isset($_GET['uid'])) {
    $uid_rfid = $_GET['uid'];
    // Chama a função para verificar e lidar com o registro
    checkAndHandleRegistration($uid_rfid, $pdo);
} else {
    echo "Nenhum UID recebido.";
}

?>
