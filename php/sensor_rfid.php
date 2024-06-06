<?php
// Inclui a biblioteca php-serial
require_once 'php_serial.class.php';
include "functions.php";

try {
    // Inicializa o objeto phpSerial
    $serial = new phpSerial();

    // Configura a porta serial (COM3 no Windows)
    $serial->deviceSet('COM5');

    // Configurações da porta serial
    $serial->confBaudRate(9600); // Velocidade de comunicação
    $serial->confParity('none'); // Sem paridade
    $serial->confCharacterLength(8); // 8 bits de dados
    $serial->confStopBits(1); // 1 bit de parada
    $serial->confFlowControl('none'); // Sem controle de fluxo

    // Abre a porta serial
    $serial->deviceOpen();

    checkAndHandleRegistration("33 35 31 66 64 38 33 66 0a" , $pdo);

    echo "Listening to the serial port...\n";

    // Lê os dados da porta serial
    while (true) {
        $read = $serial->readPort();
        if ($read) {
            echo "Dados recebidos: " . $read;

            
        }
        usleep(100000); // Aguarda 100ms antes de ler novamente
    }
} catch (Exception $e) {
    // Trate qualquer exceção que ocorra
    echo "An error occurred: " . $e->getMessage() . "\n";
} finally {
    // Fecha a porta serial no final, independentemente de ocorrer uma exceção ou não
    if (isset($serial)) {
        $serial->deviceClose();
    }
}

