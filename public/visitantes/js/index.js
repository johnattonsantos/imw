$(document).ready(function() {
    // pega os valores dos parametros de pesquisa
    function getSearchParameters() {
        return {
            search: $('#searchInput').val(),
            excluido: $('input[name="excluido"]:checked').val(),
        }
    }

    // ativa as ações dos botões na datatable
    function activeActions() {
        $('.btn-confirm-delete').on('click', function() {
            const formId = $(this).data('form-id')
            swal({
                title: __('Deseja realmente apagar os registros deste visitante?'),
                type: 'error',
                showCancelButton: true,
                confirmButtonText: __('Deletar'),
                confirmButtonColor: "#d33",
                cancelButtonText: __('Cancelar'),
                cancelButtonColor: "#3085d6",
                padding: '2em'
            }).then(function(result) {
                if (result.value) document.getElementById(formId).submit()
            })
        })
        $('.btn-visualizar').click(function () {
            $('#visualizarVisitantesModal').modal('show')
            console.log($(this).data('membro-id'), "id")
            $.ajax({
                type: "get",
                url: "/membresia-geral/visualizar-html/" + $(this).data('membro-id'),
                beforeSend: function () {
                    $('#visualizarVisitantesModal .modal-content').html('<div class="modal-body" style="min-height: 200px"></div>');
                    $('.loadable').block({
                        message: '<div class="spinner-border mr-2 text-secondary align-self-center loader-sm"></div>',
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
                    $('#visualizarVisitantesModal .modal-content').html(html);
                },
                error: function (error) {
                    $('#visualizarVisitantesModal').modal('hide');
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
        ajax: {
            url: $('#datatable').data('url'),
            data: function (d) {
                d.parameters = getSearchParameters();
            }
        },
        columns: [
            {data: 'nome', name: 'nome'},
            {data: 'telefone_preferencial', name: 'telefone_preferencial'},
            {data: 'email_preferencial', name: 'email_preferencial'},
            {data: 'updated_at', name: 'updated_at'},
        ],
        columnDefs: [
            {
                targets: 0,
                orderable: 1,
                render: function (data, type, row, meta) {
                    if (row.deleted_at) {
                        return `<span class="badge badge-danger"> ${row.nome} </span>`
                    } else if (row.has_errors) {
                        return `<span class="badge badge-warning"> ${row.nome} </span>`
                    } else {
                        return row.nome
                    }
                }
            },
            {
                targets: 4,
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
