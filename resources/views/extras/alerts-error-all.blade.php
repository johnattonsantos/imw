@if($errors->any())
<script>
    window.onload = function() {
        @foreach($errors->all() as $error)
            toastr.error(@json(__($error)));
        @endforeach
    };
</script>
@endif
