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
                title: 'Deseja realmente cancelar a transferência deste membro?',
                type: 'error',
                showCancelButton: true,
                confirmButtonText: "Sim",
                confirmButtonColor: "#d33",
                cancelButtonText: "Não",
                cancelButtonColor: "#3085d6",
                padding: '2em'
            }).then(function(result) {
                if(result.value) document.getElementById(formId).submit()
            })
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
            {data: 'validado', name: 'validado'},
        ],
        columnDefs: [
            {
                targets: 1,
                orderable: 1,
                render: function (data, type, row, meta) {
                    if (row.dt_exclusao && row.transferido) {
                        return `<span class="badge badge-secondary"> ${row.membro} (transferido(a) para ${row.igreja_atual})</span>`
                    } else if (row.dt_exclusao) {
                        return `<span class="badge badge-danger"> ${row.membro} </span>`
                    } else if (row.has_errors) {
                        return `<span class="badge badge-warning"> ${row.membro} </span>`
                    } else if (row.notificacao_transferencia_ativa) {
                        return `<span class="font-italic text-secondary">${row.membro} (Em transferência para ${row.notificacao_transferencia_ativa.igreja_destino.nome})</span>`
                    } else {
                        return row.membro
                    }
                }
            },
            {
                targets: 6,
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
