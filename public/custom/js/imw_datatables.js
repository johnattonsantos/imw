function initDataTable(selector = '#datatable', userOptions = {}, fnDrawCallback = null) {
    const defaultOptions = {
        processing: true,
        serverSide: true,
        lengthChange: false,
        searching: false,
        pageLength: 50,
        order: [[0, 'asc']],
        
        drawCallback: function() {
            activeTooltips();
            typeof fnDrawCallback === 'function' && fnDrawCallback();
        },
        "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" 
            + "<'table-responsive'tr>" 
            + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        "oLanguage": {
            "sProcessing": '<div class="load-datatable text-primary"><div class="spinner-border mr-2 align-self-center loader-sm "></div>' + __('Carregando...') + '</div>',
            "sInfo": __('Exibindo página _PAGE_ de _PAGES_'),
            "sZeroRecords": __('Nenhum registro encontrado'),
            "oPaginate": { 
                "sPrevious": __('Anterior'), 
                "sNext": __('Próxima'),
            },
        },
    }
    const options = $.extend(true, {}, defaultOptions, userOptions);

    return $(selector).DataTable(options);
}

function activeTooltips() {
    $('.bs-tooltip').tooltip();
}
