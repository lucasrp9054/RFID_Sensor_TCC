<?php
// Conexão com o banco de dados
include "acesso_bd.php"; // Verifique se este arquivo está configurado corretamente

// Iniciar a sessão para obter a matrícula do aluno
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$ma_aluno = $_SESSION['user'];

// Consulta SQL para buscar os eventos do calendário do aluno
$sql = "
    SELECT
        gh.id_grade_horaria,
        gh.cod_dia_semana,
        gh.hora_inicio,
        gh.hora_fim,
        gh.id_disciplina,
        d.nome AS nome_disciplina,
        cd.dia_semana
    FROM
        tb_alunos_aulas aa
        INNER JOIN tb_grade_horaria gh ON aa.id_grade_horaria = gh.id_grade_horaria
        INNER JOIN tb_disciplinas d ON gh.id_disciplina = d.id_disciplina
        INNER JOIN tb_cod_dia_semana cd ON gh.cod_dia_semana = cd.cod_dia_semana
    WHERE
        aa.ma_aluno = :ma_aluno
";

// Preparar a consulta
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':ma_aluno', $ma_aluno, PDO::PARAM_STR);

// Executar a consulta
$stmt->execute();

// Obter os resultados como array associativo
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Array para armazenar os eventos
$events = [];

// Verificar se há resultados
if (!empty($result)) {
    foreach ($result as $row) {
        // Converter o código do dia da semana para o nome do dia
        switch ($row['cod_dia_semana']) {
            case 1:
                $dayOfWeek = 'Monday';
                break;
            case 2:
                $dayOfWeek = 'Tuesday';
                break;
            case 3:
                $dayOfWeek = 'Wednesday';
                break;
            case 4:
                $dayOfWeek = 'Thursday';
                break;
            case 5:
                $dayOfWeek = 'Friday';
                break;
            case 6:
                $dayOfWeek = 'Saturday';
                break;
            case 7:
                $dayOfWeek = 'Sunday';
                break;
            default:
                $dayOfWeek = ''; // Tratar caso não esperado
                break;
        }

        // Adicionar evento ao array de eventos
        if (!empty($dayOfWeek)) {
            $events[] = [
                'title' => $row['nome_disciplina'],
                'daysOfWeek' => [$dayOfWeek],
                'startTime' => $row['hora_inicio'],
                'endTime' => $row['hora_fim'],
            ];
        }
    }
}

// Converter eventos para JSON e enviar como resposta
$eventsJSON = json_encode($events);
header('Content-Type: application/json');
echo $eventsJSON;
?>
