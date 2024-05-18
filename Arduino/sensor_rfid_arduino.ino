#include <SPI.h>
#include <MFRC522.h>
#include <Ethernet.h>

#define RST_PIN         9          // Configuração do pino de reset
#define SS_PIN          10         // Configuração do pino de seleção

MFRC522 mfrc522(SS_PIN, RST_PIN);  // Criação do objeto MFRC522

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED }; // Substitua pelo endereço MAC do seu módulo Ethernet
IPAddress server(192, 168, 1, 100); // Substitua pelo endereço IP do seu servidor
EthernetClient client;

void setup() {
  Serial.begin(9600);   // Inicialização da comunicação serial
  SPI.begin();          // Inicialização da SPI
  mfrc522.PCD_Init();   // Inicialização do MFRC522
  Ethernet.begin(mac);  // Inicialização da Ethernet com o endereço MAC especificado
  delay(1000);          // Espera 1 segundo
}

void loop() {
  // Verifica se há cartões presentes
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    // Lê o UID do cartão RFID
    String uid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      uid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
      uid += String(mfrc522.uid.uidByte[i], HEX);
    }
    
    // Envia o UID do cartão RFID para o servidor
    if (client.connect(server, 80)) {
      client.print("GET /sensor_rfid.php?uid=");
      client.print(uid);
      client.println(" HTTP/1.1");
      client.println("Host: 192.168.1.100"); // Substitua pelo endereço IP do seu servidor
      client.println("Connection: close");
      client.println();
      client.stop();
    }
  }
  delay(1000);  // Aguarda 1 segundo antes de ler o próximo cartão
}
