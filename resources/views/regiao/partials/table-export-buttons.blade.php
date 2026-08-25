@php
    $exportTableId = $tableId ?? 'report-table';
    $exportTitle = $title ?? __('Relatório');
    $exportFilename = $filename ?? 'relatorio';
    $exportSheetName = $sheetName ?? __('Relatório');
@endphp

<div class="mb-3">
    <button type="button" class="btn btn-primary btn-rounded"
        onclick='imwExportTableExcel(@json($exportTableId), @json($exportFilename), @json($exportSheetName))'>
        <i class="fas fa-file-excel"></i> {{ __('Excel') }}
    </button>
    <button type="button" class="btn btn-primary btn-rounded"
        onclick='imwExportTablePdf(@json($exportTableId), @json($exportFilename), @json($exportTitle))'>
        <i class="fas fa-file-pdf"></i> {{ __('PDF') }}
    </button>
</div>
