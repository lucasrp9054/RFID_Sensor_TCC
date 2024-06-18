<?php
// Inclui o arquivo que contém a função existe_dados_vazios e a conexão com o banco de dados
include '../../funcoes/registro/registro_functions.php';
include '../../bd/acesso_bd.php';

// Verifica se o formulário foi submetido via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o campo 'ma' foi enviado
    if (isset($_POST['ma'])) {
        // Captura o MA enviado pelo formulário
        $ma = $_POST['ma'];
        
        // Chama a função existe_dados_vazios para processar o MA
        existe_dados_vazios($ma, $pdo);

        // Exemplo de redirecionamento após o processamento
        header("Location: login.php");
        exit;
    } else {
        // Caso o campo 'ma' não tenha sido enviado, faça algo (exibir mensagem de erro, redirecionar de volta etc.)
    }
}
?>
