$(document).ready(function() {

    $('.btn-confirm-delete').on('click', function() {
        const formId = $(this).data('form-id')
        swal({
            title: __('Deseja realmente apagar os registros desta Carta Pastoral?'),
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
        $('#visualizarGCEUCartaPastoralModal').modal('show')
        $.ajax({
            type: "get",
            url: "/gceu/carta-pastoral/visualizar-html/" + $(this).data('cartapastoralid'),
            beforeSend: function () {
                $('#visualizarGCEUCartaPastoralModal .modal-content').html('<div class="modal-body" style="min-height: 200px"></div>');
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
                $('#visualizarGCEUCartaPastoralModal .modal-content').html(html);
            },
            error: function (error) {
                $('#visualizarGCEUCartaPastoralModal').modal('hide');
                toastr.error(__('Erro ao visualizar dados dessa carta pastoral.'));
            },
            complete: function () {
                $('.loadable').unblock();
            }
        });
    })
  

    // atrela a pesquisa pelo formulário ao datatable
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
    });
});
