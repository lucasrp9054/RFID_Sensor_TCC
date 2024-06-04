<?php

require "vendor/autoload.php";
include "functions.php";
include "acesso_bd.php";

use Lepiaf\Serial\Serial;

$serial = new Serial();
$serial->deviceSet("COM3"); // Altere para a porta serial correta
$serial->confBaudRate(9600); // Taxa de transmissão
$serial->confParity("none"); // Paridade
$serial->confCharacterLength(8); // Comprimento do caractere
$serial->confStopBits(1); // Bits de parada

$serial->deviceOpen();

while (true) {
    // Lê o UID recebido pela porta serial
    $uid = trim($serial->readPort());

    // Chama a função verificarUid com o UID e a conexão PDO
    checkAndHandleRegistration($uid, $pdo);
}

$serial->deviceClose();

?>
