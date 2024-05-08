<?php
// Verificar se foi recebido um UID do cartão RFID na solicitação GET
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];

    // Conectar-se ao banco de dados
    include "acesso_bd.php";

    // Incluir o arquivo functions.php para ter acesso à função checkAndHandleRegistration()
    include "functions.php";

    // Chamar a função checkAndHandleRegistration() com o UID do cartão RFID
    checkAndHandleRegistration($uid, $pdo);
} else {
    // Se nenhum UID do cartão RFID foi recebido na solicitação GET
    echo "Nenhum UID do cartão RFID recebido.";
}
?>
