import serial
import time
import requests

def send_uid_to_php(uid):
    url = 'http://localhost/conexao_python_php.php'  # URL do seu endpoint PHP
    data = {'uid': uid}
    response = requests.post(url, data=data)
    return response.text

try:
    # Configura a porta serial
    serialInst = serial.Serial()
    serialInst.baudrate = 9600
    serialInst.port = "COM5"  # Ajustar para a porta correta
    serialInst.open()

    print("Listening to the serial port...")

    # Lê os dados da porta serial
    while True:
        if serialInst.in_waiting:
            uid = serialInst.readline().decode('utf-8').rstrip('\n')
            if uid:
                print(f"Dados recebidos: {uid}")
                # Envia o UID para o script PHP
                response = send_uid_to_php(uid)
                print(f"Resposta do PHP: {response}")

        time.sleep(0.1)  # Aguarda 100ms antes de ler novamente

except Exception as e:
    # Trate qualquer exceção que ocorra
    print(f"An error occurred: {e}")

finally:
    # Fecha a porta serial no final, independentemente de ocorrer uma exceção ou não
    if serialInst.is_open:
        serialInst.close()
