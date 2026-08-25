@once
    <script src="{{ asset('theme/assets/js/planilha/xlsx.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        function imwGetExportTableData(tableId) {
            const table = document.getElementById(tableId);
            if (!table) {
                return [];
            }

            const headers = Array.from(table.querySelectorAll('thead th')).map(function(th) {
                return th.innerText.trim();
            });

            const bodyRows = Array.from(table.querySelectorAll('tbody tr')).map(function(row) {
                return Array.from(row.querySelectorAll('th, td')).map(function(cell) {
                    return cell.innerText.trim();
                });
            });

            const footerRows = Array.from(table.querySelectorAll('tfoot tr')).map(function(row) {
                return Array.from(row.querySelectorAll('th, td')).map(function(cell) {
                    return cell.innerText.trim();
                });
            });

            return [headers].concat(bodyRows, footerRows).filter(function(row) {
                return row.length > 0;
            });
        }

        function imwNormalizeExportFilename(filename, extension) {
            const safeName = filename || 'relatorio';
            return safeName.endsWith(extension) ? safeName : safeName + extension;
        }

        function imwExportTableExcel(tableId, filename, sheetName) {
            const data = imwGetExportTableData(tableId);
            if (data.length <= 1 || typeof XLSX === 'undefined') {
                return;
            }

            const worksheet = XLSX.utils.aoa_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, sheetName || 'Relatório');
            XLSX.writeFile(workbook, imwNormalizeExportFilename(filename, '.xlsx'));
        }

        function imwExportTablePdf(tableId, filename, title) {
            const data = imwGetExportTableData(tableId);
            if (data.length <= 1 || typeof pdfMake === 'undefined') {
                return;
            }

            pdfMake.createPdf({
                pageOrientation: 'landscape',
                pageSize: 'A4',
                content: [
                    { text: title || 'Relatório', style: 'header' },
                    {
                        table: {
                            headerRows: 1,
                            widths: Array(data[0].length).fill('*'),
                            body: data
                        }
                    }
                ],
                styles: {
                    header: {
                        fontSize: 14,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    }
                },
                defaultStyle: {
                    fontSize: 9
                }
            }).download(imwNormalizeExportFilename(filename, '.pdf'));
        }
    </script>
@endonce
