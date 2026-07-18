
@if(session('success'))
<script>
    window.onload = function(e) {
        toastr.success(@json(__(session('success'))));
    };
</script>
@endif

@if(session('error'))
<script>
    window.onload = function(e) {
        toastr.error(@json(__(session('error'))));
    };
</script>
@endif

@if(session('status'))
<script>
    window.onload = function(e) {
        toastr.info(@json(__(session('status'))));
    };
</script>
@endif

@if(session('warning'))
<script>
    window.onload = function(e) {
        toastr.warning(@json(__(session('warning'))));
    };
</script>
@endif
