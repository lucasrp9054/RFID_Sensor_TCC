jQuery(document).ready(function() {
    // Configuração inicial do FullCalendar
    jQuery('#calendar').fullCalendar({
		locale: 'pt-br',
        themeSystem: 'bootstrap4',
        businessHours: false,
        defaultView: 'month',
        editable: true,
        header: {
            left: 'title',
            center: 'month,agendaWeek,agendaDay',
            right: 'today prev,next'
        },
        // Eventos do calendário
        events: [
            {
                title: 'Barbeiro',
                description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                start: '2024-06-24',
                end: '2024-06-24',
                className: 'fc-bg-default',
                icon : "circle"
            },
            {
                title: 'Voo para Paris',
                description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                start: '2024-06-25T14:00:00',
                end: '2024-06-25T20:00:00',
                className: 'fc-bg-deepskyblue',
                icon : "cog",
                allDay: false
            }
            // Adicione mais eventos conforme necessário
        ],
        // Função chamada ao clicar em um dia do calendário
        dayClick: function() {
            jQuery('#modal-view-event-add').modal();
        },
        // Função chamada ao clicar em um evento do calendário
        eventClick: function(event, jsEvent, view) {
            jQuery('.event-icon').html("<i class='fa fa-"+event.icon+"'></i>");
            jQuery('.event-title').html(event.title);
            jQuery('.event-body').html(event.description);
            jQuery('.eventUrl').attr('href', event.url);
            jQuery('#modal-view-event').modal();
        }
        // Definindo o locale para Português do Brasil
        
    });
});
