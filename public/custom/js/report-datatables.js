(function (window, $) {
    'use strict';
    if (!$ || window.IMWReportTables) return;
    const script = document.currentScript;
    const source = script && script.getAttribute('data-datatables-src');
    const selector = 'table[data-report-table]';
    const language = {
        decimal: ',', thousands: '.', emptyTable: 'Nenhum registro encontrado',
        info: 'Mostrando de _START_ at\u00e9 _END_ de _TOTAL_ registros',
        infoEmpty: 'Mostrando 0 at\u00e9 0 de 0 registros',
        infoFiltered: '(filtrados de _MAX_ registros)',
        lengthMenu: 'Exibir _MENU_ resultados por p\u00e1gina', search: 'Pesquisar:',
        loadingRecords: 'Carregando...', processing: 'Processando...',
        zeroRecords: 'Nenhum registro encontrado',
        paginate: {first: 'Primeiro', last: '\u00daltimo', next: 'Pr\u00f3ximo', previous: 'Anterior'},
        aria: {sortAscending: ': ordenar crescente', sortDescending: ': ordenar decrescente'}
    };

    function text(value) {
        return $('<div>').html(String(value)).text().replace(/\u00a0/g, ' ').trim();
    }

    function registerTypes() {
        $.fn.dataTable.ext.type.detect.unshift(function (value) {
            const clean = text(value);
            return /^\d{2}\/\d{2}\/\d{4}(?: \d{2}:\d{2}(?::\d{2})?)?$/.test(clean) ? 'imw-date-br' : null;
        });
        $.fn.dataTable.ext.type.order['imw-date-br-pre'] = function (value) {
            const parts = text(value).split(/[\/ :]/);
            return Number(parts[2] + parts[1] + parts[0] + (parts[3] || '00') + (parts[4] || '00') + (parts[5] || '00'));
        };
        $.fn.dataTable.ext.type.detect.unshift(function (value) {
            const clean = text(value).replace(/^R\$\s*/, '');
            return clean === '' || clean === '-' || /^-?(?:\d+|\d{1,3}(?:\.\d{3})+)(?:,\d+)?%?$/.test(clean) ? 'imw-number-br' : null;
        });
        $.fn.dataTable.ext.type.order['imw-number-br-pre'] = function (value) {
            return Number(text(value).replace(/R\$|\s|\.|%/g, '').replace(',', '.')) || 0;
        };
    }

    function init(target) {
        if (!$.fn.dataTable) return;
        $(target || selector).each(function () {
            if ($.fn.dataTable.isDataTable(this)) return;
            const table = this;
            if (!table.parentElement.classList.contains('imw-report-table-wrapper')) {
                $(table).wrap('<div class="imw-report-table-wrapper"></div>');
            }
            $(table).DataTable({
                language: language, pageLength: 25, lengthMenu: [10, 25, 50, 100],
                order: [], autoWidth: false, pagingType: 'full_numbers',
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'table-responsive't><'row'<'col-sm-5'i><'col-sm-7'p>>"
            });
            if (!table.dataset.reportEditBound) {
                table.addEventListener('focusout', function (event) {
                    const cell = event.target.closest('td[contenteditable]');
                    if (cell && $.fn.dataTable.isDataTable(table)) {
                        $(table).DataTable().row(cell.closest('tr')).invalidate('dom');
                    }
                });
                table.dataset.reportEditBound = '1';
            }
        });
    }

    function snapshot(table) {
        const clone = table.cloneNode(false);
        if (table.tHead) clone.appendChild(table.tHead.cloneNode(true));
        const body = document.createElement('tbody');
        const rows = $.fn.dataTable && $.fn.dataTable.isDataTable(table)
            ? $(table).DataTable().rows({search: 'applied', order: 'applied'}).nodes().toArray()
            : Array.from(table.tBodies[0] ? table.tBodies[0].rows : []);
        rows.forEach(function (row) { body.appendChild(row.cloneNode(true)); });
        clone.appendChild(body);
        if (table.tFoot) clone.appendChild(table.tFoot.cloneNode(true));
        clone.removeAttribute('id');
        return clone;
    }

    window.IMWReportTables = {
        init: init,
        snapshot: snapshot,
        destroy: function (target) {
            $(target).each(function () {
                if ($.fn.dataTable && $.fn.dataTable.isDataTable(this)) $(this).DataTable().destroy();
                const parent = this.parentElement;
                if (parent.classList.contains('imw-report-table-wrapper')) $(this).unwrap();
            });
        },
        exportExcel: function (XLSX) {
            const workbook = XLSX.utils.book_new();
            $(selector).each(function (index) {
                XLSX.utils.book_append_sheet(workbook, XLSX.utils.table_to_sheet(snapshot(this), {raw: true}), 'Relatorio ' + (index + 1));
            });
            if (workbook.SheetNames.length) XLSX.writeFile(workbook, 'relatorio_' + new Date().toISOString().slice(0, 10) + '.xlsx');
        }
    };

    $(function () {
        if (!document.querySelector(selector)) return;
        if ($.fn.dataTable) {
            registerTypes();
            init();
        } else if (source) {
            $.getScript(source).done(function () { registerTypes(); init(); });
        }
    });
})(window, window.jQuery);
