#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN 10
#define RST_PIN 9

MFRC522 mfrc522(SS_PIN, RST_PIN); // Cria uma instância do MFRC522

void setup() {
  Serial.begin(9600); // Inicializa a comunicação serial
  SPI.begin(); // Inicializa a SPI bus
  mfrc522.PCD_Init(); // Inicializa o MFRC522
}

void loop() {
  // Verifica se um novo cartão foi detectado
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    // Lê o UID do cartão
    String uid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      // Converte cada byte do UID em uma string hexadecimal e concatena
      // Adiciona um 0 à esquerda se o byte for menor que 0x10
      uid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
      uid += String(mfrc522.uid.uidByte[i], HEX);
    }
    Serial.println(uid); // Envia o UID pela porta serial
    delay(1000); // Aguarda 1 segundo antes de ler outro cartão
  }
}