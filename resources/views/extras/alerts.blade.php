
@if(session('success'))
<script>
    window.onload = function(e) {
        toastr.success("{{ session('success') }}");
    };
</script>
@endif

@if(session('error'))
<script>
    window.onload = function(e) {
        toastr.error("{{ session('error') }}");
    };
</script>
@endif

@if(session('warning'))
<script>
    window.onload = function(e) {
        toastr.warning(@json(session('warning')));
    };
</script>
@endif
