@extends('template.layout')

@section('breadcrumb')
    <x-breadcrumb :breadcrumbs="[
        ['text' => 'Home', 'url' => '/', 'active' => false],
        ['text' => 'Relatórios - Financeiro', 'url' => '#', 'active' => false],
        ['text' => 'Livro Grade', 'url' => '#', 'active' => true],
    ]"></x-breadcrumb>
@endsection

@section('extras-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('theme/assets/css/elements/alert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .year-btn {
            width: auto;
            /* Defina a largura automática */
            position: relative;
            /* Necessário para o posicionamento absoluto do ícone */
        }

        .check-icon {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
        }

        .editing {
            border: 1px solid #ccc;
            /* Adiciona uma borda cinza suave */
            background-color: #fff;
            /* Define o fundo como branco */
            border-radius: 4px;
            /* Adiciona cantos arredondados */
            padding: 4px 8px;
            /* Adiciona um espaçamento interno */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Adiciona uma sombra suave */
            transition: border-color 0.3s, background-color 0.3s;
            /* Adiciona uma transição suave */
        }
    </style>
@endsection

@include('extras.alerts')

@section('content')
    <div class="col-lg-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>Relatório Livro Grade - {{ session('session_perfil')->instituicao_nome }}
                        </h4>
                    </div>
                </div>

            </div>
            <div class="widget-content widget-content-area">

                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">

                        @php
                            $currentYear = \Carbon\Carbon::now()->year;
                        @endphp

                        @for ($i = 0; $i < 7; $i++)
                            @php
                                $year = $currentYear - $i;
                            @endphp
                            <button class="btn mb-4 mr-2 btn-lg year-btn btn-primary {{ $year == $currentYear ? ' btn-success' : '' }}" data-year="{{ $year }}">
                                <span class="btn-content">{{ $year }}</span>
                                @if ($year == $currentYear)
                                    <i class="fas fa-check"></i>
                                @endif
                            </button>
                        @endfor
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-center align-items-center">
                        <div id="loadingIndicator" class="d-none text-center" style="font-size: 3em;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>

                    <div class="col-xl-12 col-md-12 col-sm-12 col-12 table-responsive">
                        <!-- Alerta de dados não encontrados -->
                        <div id="noDataAlert" class="alert alert-danger d-none" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M0 14.42l.719-1.24L7.998 1.58l7.283 11.58.719 1.24H0zm8-1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-.002-3a1 1 0 1 0-.001-2 1 1 0 0 0 0 2z" />
                            </svg> {{ __('Nenhum dado encontrado.') }}
                        </div>

                        <!-- Alerta de erro -->
                        <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill mr-2" viewBox="0 0 16 16">
                                <path
                                    d="M0 14.42l.719-1.24L7.998 1.58l7.283 11.58.719 1.24H0zm8-1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-.002-3a1 1 0 1 0-.001-2 1 1 0 0 0 0 2z" />
                            </svg> {{ __('Ocorreu um erro ao carregar os dados. Por favor, tente novamente mais tarde.') }}
                        </div>
                        <input type="hidden" id="get-ano">
                        <table id="livrograde" class="table table-striped" style="font-size: 90%; margin-top: 15px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="50px">{{ __('ROL') }}</th>
                                    <th width="450px">{{ __('DIZIMISTA') }}</th>
                                    <th width="100px">{{ __('JAN') }}</th>
                                    <th width="100px">{{ __('FEV') }}</th>
                                    <th width="100px">{{ __('MAR') }}</th>
                                    <th width="100px">{{ __('ABR') }}</th>
                                    <th width="100px">{{ __('MAI') }}</th>
                                    <th width="100px">{{ __('JUN') }}</th>
                                    <th width="100px">{{ __('JUL') }}</th>
                                    <th width="100px">{{ __('AGO') }}</th>
                                    <th width="100px">{{ __('SET') }}</th>
                                    <th width="100px">{{ __('OUT') }}</th>
                                    <th width="100px">{{ __('NOV') }}</th>
                                    <th width="100px">{{ __('DEZ') }}</th>
                                    <th width="100px">{{ __('13º') }}</th>
                                    <th width="50px">{{ __('TOTAL') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <button class="btn btn-success " onclick="exportReportToExcel();"><i
                            class="fa fa-file-excel" aria-hidden="true"></i> {{ __('Excel') }}</button>
                             <button class="btn btn-secondary " onclick="gerarPdf()"> <i class="fa fa-file-pdf"></i> {{ __('PDF') }}</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="{{ asset('theme/assets/js/planilha/papaparse.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/FileSaver.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/planilha/planilha.js') }}"></script>
    <script src="{{ asset('theme/assets/js/pages/movimentocaixa.js') }}"></script>
    <script src="{{ asset('theme/plugins/jsPDF/assets/jspdf.js') }}"></script>
    <script src="{{ asset('theme/plugins/jsPDF/assets/mitubachi-normal.js') }}"></script>
    <script src="{{ asset('theme/plugins/jsPDF/assets/faker.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/jsPDF/dist/jspdf.plugin.autotable.js') }}"></script>
    <script src="{{ asset('theme/plugins/jsPDF/examples.js') }}"></script>
@endsection
@section('extras-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
    <script>
        $(document).ready(function() {
            let year = $('.year-btn.btn-success').data('year');
            $('#get-ano').val(year);
            $('.year-btn').click(function() {
                $('.year-btn').removeClass('btn-success').find('i').remove();
                $(this).addClass('btn-success').append('<i class="fas fa-check"></i>');
                var year = $(this).data('year');
                $('#get-ano').val(year);
                fetchData(year);
            });

            // Obter o ano atual ao abrir a página
            var currentYear = new Date().getFullYear();
            fetchData(currentYear);

            // Adicionar evento de edição aos campos editáveis
            $('#livrograde tbody').on('focus', 'td[contenteditable="true"]', function() {
                var $this = $(this);
                // Aplica a máscara de R$
                $this.inputmask('currency', {
                    'alias': 'numeric',
                    'groupSeparator': '.',
                    'radixPoint': ',',
                    'digits': 2,
                    'digitsOptional': false,
                    'placeholder': '0'
                });
                $(this).addClass('editing');
                // Define uma largura fixa para a célula
                $this.css('width', $this.width() + 'px');
                // Define o alinhamento à esquerda
                $this.css('text-align', 'left');
                // Adiciona a classe 'active' à linha pai (tr) da célula em edição
                $(this).closest('tr').addClass('active');
            });


            // Remover classe CSS temporária ao terminar de editar o campo
            $('#livrograde tbody').on('blur', 'td[contenteditable="true"]', function() {
                var $this = $(this);
                $this.inputmask('remove');

                // Verifica se o campo está vazio e define como "0,00" se estiver
                if ($this.text().trim() === '') {
                    $this.text('0,00');
                }
                // Remove a classe 'active' da linha pai (tr) da célula ao perder o foco
                $this.closest('tr').removeClass('active');

                // Localizar a célula em edição
                var $editingCell = $('#livrograde tbody td.editing');

                $this.removeClass('editing');

                // Verificar se encontrou a célula em edição
                if ($editingCell.length > 0) {
                    // Obter o valor da célula
                    var cellValue = $editingCell.text().trim();

                    var columnIndex = $editingCell.index();

                    var columnHeader = $('#livrograde thead th').eq(columnIndex).text().trim().toUpperCase();

                     // Obter o membro_id da linha
                    var memberId = $editingCell.closest('tr').attr('id');

                    // Enviar os dados para a API
                    sendToAPI(cellValue, columnHeader, memberId);
                } else {
                    console.log('Nenhuma célula está em edição.');
                }

                // Atualizar o valor total da linha após editar
                updateRowTotal($this.closest('tr'));
            });
        });


        

        function gerarPdf() {
            
            const doc = new jspdf.jsPDF({
                orientation: 'l', // 'l' for landscape, 'p' for portrait
                unit: 'mm',
                format: 'a4',
                headStyles: {
                fillColor: [241, 196, 15],
                fontSize: 15,
                },
            })
            const totalPagesExp = doc.internal.getNumberOfPages();
            doc.autoTable({ 
                
                head: headRows(),
                body: bodyRows(40),
                willDrawPage: function (data) {
                    // Header
                    doc.setFontSize(20)
                    doc.setTextColor(40)
                    doc.text(`Relatório Livro Grade - {{ session('session_perfil')->instituicao_nome }} - Ano: ${$('#get-ano').val()}`, 15, 22)
                },
                didDrawPage: function (data) {
                    // Footer
                    let str = 'Página ' + doc.internal.getNumberOfPages()
                    // Total page number plugin only available in jspdf v1.0+
                    if (typeof doc.putTotalPages === 'function') {
                        str = str + ' de ' + totalPagesExp
                    }
                    doc.setFontSize(10)

                    // jsPDF 1.4+ uses getHeight, <1.4 uses .height
                    const pageSize = doc.internal.pageSize
                    const pageHeight = pageSize.height
                        ? pageSize.height
                        : pageSize.getHeight()
                    doc.text(str, data.settings.margin.left, pageHeight - 10)
                },
                headStyles: { fillColor: [52, 58, 64] },
                margin: { top: 30 },

                head: headRows(),
                body: bodyRows(5),
                theme: 'grid',
                html: '#livrograde' 
            })

            doc.save('table.pdf')
        }


        function sendToAPI(value, title, memberId) {
            // Criar os dados a serem enviados para a API
            var postData = {
                value: value,
                title: title,
                ano: $('.year-btn.btn-success').data('year'),
                membro_id: memberId,
            };

            // Enviar os dados para a API via POST
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{route('financeiro.livrograde.store')}}",
                method: 'POST',
                data: postData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    console.log('Dados enviados com sucesso para a API:', postData);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao enviar dados para a API:', error);
                }
            });
        }

        function fetchData(year) {
            // Exibir indicador de carregamento
            $('#livrograde').addClass('d-none');
            $('#loadingIndicator').removeClass('d-none');
            $('#errorAlert').addClass('d-none');

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('financeiro.livrogradepost') }}",
                method: 'POST',
                data: {
                    ano: year
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    $('#livrograde').removeClass('d-none');
                    $('#noDataAlert').addClass('d-none');
                    $('#livrograde tbody').empty();

                    response.forEach(function(row) {
                        var newRow = '<tr id="' + row.id + '">';
                        newRow += '<td>' + row.rol_atual + '</td>';
                        newRow += '<td>' + row.nome + '</td>';
                        newRow += '<td contenteditable="true">' + row.jan + '</td>';
                        newRow += '<td contenteditable="true">' + row.fev + '</td>';
                        newRow += '<td contenteditable="true">' + row.mar + '</td>';
                        newRow += '<td contenteditable="true">' + row.abr + '</td>';
                        newRow += '<td contenteditable="true">' + row.mai + '</td>';
                        newRow += '<td contenteditable="true">' + row.jun + '</td>';
                        newRow += '<td contenteditable="true">' + row.jul + '</td>';
                        newRow += '<td contenteditable="true">' + row.ago + '</td>';
                        newRow += '<td contenteditable="true">' + row.set + '</td>';
                        newRow += '<td contenteditable="true">' + row.out + '</td>';
                        newRow += '<td contenteditable="true">' + row.nov + '</td>';
                        newRow += '<td contenteditable="true">' + row.dez + '</td>';
                        newRow += '<td contenteditable="true">' + row.o13 + '</td>';
                        newRow += '<td class="total-column">' + row.valor_total + '</td>';
                        newRow += '</tr>';

                        $('#livrograde tbody').append(newRow);
                    });

                    $('#loadingIndicator').addClass('d-none');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    $('#errorAlert').html(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle-fill mr-2" viewBox="0 0 16 16"><path d="M0 14.42l.719-1.24L7.998 1.58l7.283 11.58.719 1.24H0zm8-1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-.002-3a1 1 0 1 0-.001-2 1 1 0 0 0 0 2z"/></svg>Ocorreu um erro ao carregar os dados. Por favor, tente novamente mais tarde.'
                    );
                    $('#errorAlert').removeClass('d-none');

                    $('#loadingIndicator').addClass('d-none');
                }
            });
        }

        // Função para atualizar o valor total da linha após a edição
        function updateRowTotal($row) {

            var total = 0;
            $row.find('td[contenteditable="true"]').each(function() {
                var value = parseFloat($(this).text().replace(',', '.')) || 0;
                total += value;
            });

            $row.find('.total-column').text(total.toFixed(2));

            // Atualizar o valor total no servidor
            var memberId = $row.attr('id');
            var valorTotal = total.toFixed(2);
        }
    </script>
@endsection
