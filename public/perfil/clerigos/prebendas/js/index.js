$(document).ready(function() {
  $('.btn-confirm-delete').on('click', function() {
    const formId = $(this).data('form-id')
    swal({
      title: __('Deseja realmente apagar os registros deste prebenda?'),
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

  initDataTable('#datatable', { serverSide: false });
});
