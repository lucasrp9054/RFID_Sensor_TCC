import serial
import time
import requests

def send_uid_to_php(uid):
    url = 'http://localhost/conexao_python_php.php'  # URL do seu endpoint PHP
    data = {'uid': uid}
    response = requests.post(url, data=data)
    return response.text

# Configura a porta serial
serialInst = serial.Serial()
serialInst.baudrate = 9600
serialInst.port = "COM5"  # Ajustar para a porta correta
serialInst.open()

print("Listening to the serial port...")

# Lê os dados da porta serial e envia para o script PHP
while True:
    if serialInst.in_waiting:
        uid = serialInst.readline().decode('utf-8').rstrip('\n')
        if uid:
            # Envia o UID para o script PHP
            response = send_uid_to_php(uid)

    time.sleep(0.1)  # Aguarda 100ms antes de ler novamente
