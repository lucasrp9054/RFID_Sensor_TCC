<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Grade Horária</title>
    <!-- Incluir CSS do FullCalendar (substitua o caminho conforme necessário) -->
	<link rel="stylesheet" type="text/css" href="src/plugins/fullcalendar/fullcalendar.css">
    <!-- Incluir jQuery (substitua o caminho conforme necessário) -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <!-- Incluir Moment.js (necessário para o FullCalendar) -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
    <!-- Incluir localização em português do FullCalendar -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/pt-br.js'></script>
    <!-- Incluir JavaScript do FullCalendar (substitua o caminho conforme necessário) -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <!-- Script para inicializar o FullCalendar -->
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next,today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia',
                    prev: 'Anterior',
                    next: 'Próximo'
                },
                locale: 'pt-br', // Configuração do idioma para português do Brasil
                events: {
                    url: 'buscar_grade_aluno.php',
                    method: 'POST',
                    success: function(data) {
                        var events = data;
                        $('#calendar').fullCalendar('addEventSource', events);
                    }
                },
                // Callback para ajustar o título do mês para começar com maiúscula
                viewRender: function(view, element) {
                    var monthName = $('#calendar').fullCalendar('getView').title;
                    var capitalizedMonth = monthName.charAt(0).toUpperCase() + monthName.slice(1);
                    $('.fc-center .fc-toolbar h2').text(capitalizedMonth);
                }
            });
        });
    </script>
    <style>
        /* Estilos opcionais para o calendário */
        body {
            margin: 40px 10px;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h2>Calendário de Grade Horária</h2>
    <div id='calendar'></div> <!-- Onde o calendário será renderizado -->
</body>
</html>
