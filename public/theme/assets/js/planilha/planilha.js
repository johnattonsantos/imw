function exportReportToExcel() {
    if (window.IMWReportTables && document.querySelector('table[data-report-table]')) {
        window.IMWReportTables.exportExcel(XLSX);
        return;
    }
    const table = document.querySelector("table");
    const rows = table.querySelectorAll("tr");

    const headers = [...rows[0].querySelectorAll("th")].map(th => th.innerText.trim());
    const totalCols = headers.length;

    const data = [];
    const colunasMonetarias = new Set();
    const formatoBR = /^\d{1,3}((\.\d{3})*)?,\d{2}$/;

    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].querySelectorAll("td");
        const rowData = {};

        const linhaDesalinhada = cells.length === totalCols - 1;

        for (let j = 0; j < totalCols; j++) {
            let valor;

            if (linhaDesalinhada && j === 1) {
                // Preenche segunda coluna com valor vazio se estiver ausente
                valor = '';
            } else {
                const cellIndex = linhaDesalinhada && j > 1 ? j - 1 : j;
                const cell = cells[cellIndex];
                valor = cell ? cell.innerText.trim() : '';
            }

            if (formatoBR.test(valor)) {
                colunasMonetarias.add(j);
                valor = parseFloat(valor.replace(/\./g, '').replace(',', '.'));
            } else if (valor === '') {
                valor = null;
            }

            rowData[headers[j]] = valor;
        }

        data.push(rowData);
    }

    const ws = XLSX.utils.json_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Relatório");

    const range = XLSX.utils.decode_range(ws['!ref']);
    const colunas = [...colunasMonetarias].map(index => XLSX.utils.encode_col(index));

    for (const col of colunas) {
        for (let R = 1; R <= range.e.r; ++R) {
            const cellAddress = col + (R + 1);
            const cell = ws[cellAddress];

            if (cell && typeof cell.v === 'number') {
                const arredondado = Number(cell.v.toFixed(2));
                cell.v = `R$ ${arredondado.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })}`;
                cell.t = 's';
            }

            if (cell && (cell.v === null || cell.v === undefined || cell.v === '')) {
                delete cell.v;
                cell.t = 's';
            }
        }
    }

    // Captura o título do relatório no <h6 class="mt-3">
    const tituloOriginal = document.querySelector('h6.mt-3')?.innerText || 'relatorio';

    const nomeBase = tituloOriginal
        .normalize("NFD").replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-zA-Z0-9\s\-]/g, '')
        .replace(/\s+/g, '_')
        .toLowerCase();

    const agora = new Date();
    const ano = agora.getFullYear();
    const mes = String(agora.getMonth() + 1).padStart(2, '0');
    const dia = String(agora.getDate()).padStart(2, '0');
    const hora = String(agora.getHours()).padStart(2, '0');
    const minuto = String(agora.getMinutes()).padStart(2, '0');

    const timestamp = `${ano}-${mes}-${dia}_${hora}-${minuto}`;
    const nomeFinal = `${nomeBase}_${timestamp}.xlsx`;

    XLSX.writeFile(wb, nomeFinal);
}


// function exportReportToExcel() {
//     var table = document.querySelector("table");
//     var wb = XLSX.utils.table_to_book(table, {
//         sheet: "Sheet JS"
//     });
//     var wbout = XLSX.write(wb, {
//         bookType: 'xlsx',
//         bookSST: true,
//         type: 'binary'
//     });

//     function s2ab(s) {
//         var buf = new ArrayBuffer(s.length);
//         var view = new Uint8Array(buf);
//         for (var i = 0; i !== s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
//         return buf;
//     }

//     saveAs(new Blob([s2ab(wbout)], {
//         type: "application/octet-stream"
//     }), 'relatorio.xlsx');
// }
