@once
    @push('report-datatable-css')
        <link href="{{ asset('theme/plugins/table/datatable/datatables.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('theme/plugins/table/datatable/dt-global_style.css') }}" rel="stylesheet" type="text/css">
        <style>
            .imw-report-table-wrapper { width: 100%; min-width: 0; margin-bottom: 20px; }
            .imw-report-table-wrapper .dataTables_wrapper { width: 100%; }
            .imw-report-table-wrapper .dataTables_wrapper > .row { margin-left: 0; margin-right: 0; }
            .imw-report-table-wrapper table { width: 100% !important; }
            .imw-report-table-wrapper .table-responsive { margin-bottom: 12px; }
            .imw-report-table-wrapper .dataTables_filter input { max-width: 100%; }
            @media (max-width: 575px) {
                .imw-report-table-wrapper .dataTables_filter,
                .imw-report-table-wrapper .dataTables_length { text-align: left; }
                .imw-report-table-wrapper .pagination { flex-wrap: wrap; }
            }
        </style>
    @endpush
    @push('report-datatable-scripts')
        <script src="{{ asset('custom/js/report-datatables.js') }}" data-datatables-src="{{ asset('theme/plugins/table/datatable/datatables.js') }}"></script>
    @endpush
@endonce
