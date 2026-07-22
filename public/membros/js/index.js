$(document).ready(function() {
    // pega os valores dos parametros de pesquisa
    function getSearchParameters() {
        return {
            search: $('#searchInput').val(),
            status: $('input[name="status"]:checked').val(),
        }
    }

    // ativa as ações dos botões
    function activeActions() {
        $('.btn-cancel-notificacao-transferencia').on('click', function () {
            const formId = $(this).data('form-id')
            swal({
                title: __('Deseja realmente cancelar a transferência deste membro?'),
                type: 'error',
                showCancelButton: true,
                confirmButtonText: __('Sim'),
                confirmButtonColor: "#d33",
                cancelButtonText: __('Não'),
                cancelButtonColor: "#3085d6",
                padding: '2em'
            }).then(function(result) {
                if(result.value) document.getElementById(formId).submit()
            })
        })

        $('.btn-visualizar').click(function () {
            $('#visualizarMembroModal').modal('show')

            $.ajax({
                type: "get",
                url: "/membresia-geral/visualizar-html/" + $(this).data('membro-id'),
                beforeSend: function () {
                    $('#visualizarMembroModal .modal-content').html('<div class="modal-body" style="min-height: 200px"></div>');

                    $('.loadable').block({
                        message: '<div class="spinner-border mr-2 text-secondary align-self-center loader-sm"></div>',
                        // timeout: 2000, //unblock after 2 seconds
                        overlayCSS: {
                            backgroundColor: '#fff',
                            opacity: 0.8,
                            cursor: 'wait'
                        },
                        css: {
                            border: 0,
                            padding: 0,
                            width: '100%',
                            height: '100%',
                            padding: '80px',
                            backgroundColor: 'transparent',
                        }
                    });
                },
                success: function (html) {
                    $('#visualizarMembroModal .modal-content').html(html);
                },
                error: function (error) {
                    $('#visualizarMembroModal').modal('hide');
                    toastr.error(__('Erro ao visualizar dados desta pessoa.'));
                },
                complete: function () {
                    $('.loadable').unblock();
                }
            });
        })
    }

    // passa as opções customizadas para o DataTable
    const optionsDataTable = {
        order: [[1, 'asc']],
        ajax: {
            url: $('#datatable').data('url'),
            data: function (d) {
                d.parameters = getSearchParameters();
            }
        },
        columns: [
            {data: 'numero_rol', name: 'numero_rol'},
            {data: 'membro', name: 'membro'},
            {data: 'recepcao', name: 'recepcao'},
            {data: 'exclusao', name: 'exclusao'},
            {data: 'congregacao', name: 'congregacao'},
        ],
        columnDefs: [
            {
                targets: 1,
                orderable: 1,
                render: function (data, type, row, meta) {
                    if (row.dt_exclusao && row.transferido) {
                        return `<span class="badge badge-secondary"> ${row.membro} ${__('(transferido(a) para')} ${row.igreja_atual})</span>`
                    } else if (row.dt_exclusao) {
                        return `<span class="badge badge-danger"> ${row.membro} </span>`
                    } else if (row.has_errors) {
                        return `<span class="badge badge-warning"> ${row.membro} </span>`
                    } else if (row.notificacao_transferencia_ativa) {
                        return `<span class="font-italic text-secondary">${row.membro} (${__('Em transferência para')} ${row.notificacao_transferencia_ativa.igreja_destino.nome})</span>`
                    } else {
                        return row.membro
                    }
                }
            },
            {
                targets: 5,
                orderable: 0,
                render: function (data, type, row, meta) {
                    return `${row.actions}`
                }
            }
        ],
    }

    // inicia o DataTable
    const dataTable = initDataTable(
        '#datatable', // selector
        optionsDataTable, // options
        function() { // drawCallback
            activeActions();
        }
    );

    // atrela a pesquisa pelo formulário ao datatable
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });
});
