$(document).ready(function() {
    // pega os valores dos parametros de pesquisa
    function getSearchParameters() {
        return {
            search: $('#searchInput').val(),
        }
    }

    // ativa as ações dos botões na datatable
    function activeActions() {
        // ação de remover
        $('.btn-confirm-delete').on('click', function() {
            const formId = $(this).data('form-id')
            swal({
                title: __('Deseja realmente desativar esta igreja?'),
                type: 'error',
                showCancelButton: true,
                confirmButtonText: __('Desativar'),
                confirmButtonColor: "#d33",
                cancelButtonText: __('Cancelar'),
                cancelButtonColor: "#3085d6",
                padding: '2em'
            }).then(function(result) {
                if (result.value) document.getElementById(formId).submit()
            })
        })

        // ação de restaurar
        $('.btn-confirm-restore').on('click', function() {
            const formId = $(this).data('form-id')
            swal({
                title: __('Deseja realmente restaurar a igreja?'),
                type: 'success',
                showCancelButton: true,
                confirmButtonText: __('Restaurar'),
                confirmButtonColor: "#1abc9c",
                cancelButtonText: __('Cancelar'),
                cancelButtonColor: "#3085d6",
                padding: '2em'
            }).then(function(result) {
                if (result.value) document.getElementById(formId).submit()
            })
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
            {data: 'distrito', name: 'distrito'},
            {data: 'nome', name: 'igreja'},
            {data: 'pastor', name: 'pastor'},
            {data: 'actions', name: 'Ações'},
        ],
        columnDefs: [
            {
                targets: 1,
                orderable: 1,
                render: function (data, type, row, meta) {
                    if (row.deleted_at) {
                        return `<span class="badge badge-danger"> ${row.nome} </span>`
                    } else {
                        return row.nome
                    }
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
