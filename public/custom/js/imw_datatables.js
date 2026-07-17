function initDataTable(selector = '#datatable', userOptions = {}, fnDrawCallback = null) {
    const t = typeof window.__ === 'function' ? window.__ : function(key) { return key; };
    const datatableLanguage = $.extend(true, {}, window.IMW_DATATABLE_LANGUAGE || {}, {
        "sProcessing": '<div class="load-datatable text-primary"><div class="spinner-border mr-2 align-self-center loader-sm "></div>' + t('Carregando...') + '</div>',
        "sInfo": t('Exibindo página _PAGE_ de _PAGES_'),
        "sInfoEmpty": t('Mostrando 0 até 0 de 0 registros'),
        "sZeroRecords": t('Nenhum registro encontrado'),
        "sEmptyTable": t('Nenhum registro encontrado'),
        "oPaginate": {
            "sPrevious": t('Anterior'),
            "sNext": t('Próxima'),
        },
    });

    const defaultOptions = {
        processing: true,
        serverSide: true,
        lengthChange: false,
        searching: false,
        pageLength: 50,
        order: [[0, 'asc']],

        drawCallback: function() {
            if (typeof window.IMW_translatePage === 'function') {
                window.IMW_translatePage(document.querySelector(selector));
                window.IMW_translatePage(document.querySelector(`${selector}_wrapper`));
            }
            activeTooltips();
            typeof fnDrawCallback === 'function' && fnDrawCallback();
        },
        "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>"
            + "<'table-responsive'tr>"
            + "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        language: datatableLanguage,
        oLanguage: datatableLanguage,
    }
    const options = $.extend(true, {}, defaultOptions, userOptions);

    return $(selector).DataTable(options);
}

function activeTooltips() {
    $('.bs-tooltip').tooltip();
}
