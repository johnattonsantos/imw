@extends('template.layout')

@section('breadcrumb')
<x-breadcrumb :breadcrumbs="[
    ['text' => 'Home', 'url' => '/', 'active' => false],
    ['text' => 'Eventos', 'url' => route('eventos.index'), 'active' => false],
    ['text' => 'Agenda de Eventos', 'url' => route('eventos.agenda'), 'active' => true],
]"></x-breadcrumb>
@endsection

@section('extras-css')
<link href="{{ asset('theme/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .agenda-shell {
        --agenda-ink: #1f2b3d;
        --agenda-muted: #687386;
        --agenda-line: #e5e9f0;
        --agenda-paper: #ffffff;
        background:
            radial-gradient(circle at 95% 0, rgba(25, 118, 210, .10), transparent 25%),
            linear-gradient(180deg, #f8fafc 0, #ffffff 180px);
        border: 1px solid var(--agenda-line);
        border-radius: 10px;
        padding: 22px;
    }

    .agenda-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
    }

    .agenda-heading h4 {
        color: var(--agenda-ink);
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .agenda-heading p {
        color: var(--agenda-muted);
        margin: 0;
    }

    .agenda-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        margin-bottom: 18px;
    }

    .agenda-legend-item {
        align-items: center;
        color: var(--agenda-muted);
        display: inline-flex;
        font-size: 12px;
        font-weight: 600;
        gap: 7px;
    }

    .agenda-legend-dot {
        border-radius: 50%;
        display: inline-block;
        height: 9px;
        width: 9px;
    }

    #agenda-calendar {
        background: var(--agenda-paper);
        border-radius: 8px;
    }

    #agenda-calendar .fc-toolbar h2 {
        color: var(--agenda-ink);
        font-size: 20px;
        font-weight: 700;
        text-transform: capitalize;
    }

    #agenda-calendar .fc-button {
        background: #ffffff;
        border: 1px solid #d8dee8;
        box-shadow: none;
        color: #344054;
        text-shadow: none;
    }

    #agenda-calendar .fc-button:hover,
    #agenda-calendar .fc-state-active {
        background: #1976d2;
        border-color: #1976d2;
        color: #ffffff;
    }

    #agenda-calendar .fc-event {
        border-radius: 4px;
        cursor: pointer;
        padding: 2px 4px;
    }

    #agenda-calendar .fc-day-header,
    #agenda-calendar .fc-axis {
        color: #596579;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    #agenda-calendar td,
    #agenda-calendar th {
        border-color: var(--agenda-line);
    }

    @media (max-width: 767px) {
        .agenda-shell {
            padding: 14px;
        }

        .agenda-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        #agenda-calendar .fc-toolbar > * {
            display: block;
            float: none;
            margin-bottom: 10px;
            text-align: center;
        }

        #agenda-calendar .fc-toolbar h2 {
            font-size: 17px;
        }
    }
</style>
@endsection

@section('content')
<div class="col-lg-12 col-12 layout-spacing">
    <div class="agenda-shell box-shadow">
        <div class="agenda-heading">
            <div>
                <h4>{{ __('Agenda de Eventos') }}</h4>
                <p>{{ __('Visualize os eventos cadastrados por mês, semana ou dia.') }}</p>
            </div>
            @if (auth()->check() && auth()->user()->hasPerfilRegra('evento-novo'))
                <a href="{{ route('eventos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> {{ __('Novo evento') }}
                </a>
            @endif
        </div>

        <div class="agenda-legend" aria-label="{{ __('Legenda dos status') }}">
            <span class="agenda-legend-item"><span class="agenda-legend-dot" style="background: #d48624;"></span>{{ __('Planejado') }}</span>
            <span class="agenda-legend-item"><span class="agenda-legend-dot" style="background: #1976d2;"></span>{{ __('Confirmado') }}</span>
            <span class="agenda-legend-item"><span class="agenda-legend-dot" style="background: #27865d;"></span>{{ __('Realizado') }}</span>
            <span class="agenda-legend-item"><span class="agenda-legend-dot" style="background: #c44343;"></span>{{ __('Cancelado') }}</span>
        </div>

        <div id="agenda-calendar"></div>
    </div>
</div>

<div class="modal fade" id="eventoAgendaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" style="min-height: 180px;">{{ __('Carregando...') }}</div>
        </div>
    </div>
</div>
@endsection

@section('extras-scripts')
<script src="{{ asset('theme/plugins/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('theme/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
<script>
    $(function () {
        const eventos = @json($agendaEventos);
        const modal = $('#eventoAgendaModal');
        const modalContent = modal.find('.modal-content');
        const loadingHtml = '<div class="modal-body" style="min-height: 180px;">' + window.__('Carregando...') + '</div>';

        $('#agenda-calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText: {
                today: window.__('Hoje'),
                month: window.__('Mês'),
                week: window.__('Semana'),
                day: window.__('Dia')
            },
            monthNames: [
                window.__('janeiro'), window.__('fevereiro'), window.__('março'), window.__('abril'),
                window.__('maio'), window.__('junho'), window.__('julho'), window.__('agosto'),
                window.__('setembro'), window.__('outubro'), window.__('novembro'), window.__('dezembro')
            ],
            monthNamesShort: [
                window.__('jan'), window.__('fev'), window.__('mar'), window.__('abr'),
                window.__('mai'), window.__('jun'), window.__('jul'), window.__('ago'),
                window.__('set'), window.__('out'), window.__('nov'), window.__('dez')
            ],
            dayNames: [
                window.__('domingo'), window.__('segunda-feira'), window.__('terça-feira'), window.__('quarta-feira'),
                window.__('quinta-feira'), window.__('sexta-feira'), window.__('sábado')
            ],
            dayNamesShort: [
                window.__('dom'), window.__('seg'), window.__('ter'), window.__('qua'),
                window.__('qui'), window.__('sex'), window.__('sáb')
            ],
            firstDay: 0,
            allDayText: window.__('Dia inteiro'),
            timeFormat: 'H:mm',
            slotLabelFormat: 'H:mm',
            displayEventTime: false,
            displayEventEnd: true,
            eventLimit: true,
            eventLimitText: window.__('mais'),
            navLinks: true,
            editable: false,
            events: eventos,
            eventRender: function (event, element) {
                const tooltip = [event.statusLabel, event.purpose, event.institution, event.location]
                    .filter(function (value) { return value && value !== '-'; })
                    .join(' | ');
                element.attr('title', tooltip);
            },
            eventClick: function (event) {
                modalContent.html(loadingHtml);
                modal.modal('show');

                $.ajax({
                    url: event.detailsUrl,
                    method: 'GET',
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    success: function (html) {
                        modalContent.html(html);
                    },
                    error: function () {
                        modalContent.html(
                            '<div class="modal-header"><h5 class="modal-title">' + window.__('Detalhes do Evento') + '</h5>' +
                            '<button type="button" class="close" data-dismiss="modal" aria-label="' + window.__('Fechar') + '"><span aria-hidden="true">&times;</span></button></div>' +
                            '<div class="modal-body"><div class="alert alert-danger mb-0">' + window.__('Não foi possível carregar os detalhes do evento.') + '</div></div>'
                        );
                    }
                });

                return false;
            }
        });
    });
</script>
@endsection
