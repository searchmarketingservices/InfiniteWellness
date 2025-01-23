<div class="container-fluid">
    <div class="d-md-flex align-items-center justify-content-between mb-7">
        <h1 class="mb-0">@yield('title')</h1>
    </div>
</div>
<div class="container-fluid">
    <div class="d-flex flex-column">
        <div class="row">
            <div class="col-12">
                @include('flash::message')
            </div>
        </div>
        @include('appointments.show_fields')
    </div>
</div>
<script>
    window.print();
</script>
