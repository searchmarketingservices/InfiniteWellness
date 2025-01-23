<center>
    <div style="margin-top: 25px !important; margin-bottom: 25px !important">
        <img src="{{ asset('logo.png') }}" width="120px" alt="logo">
    </div>
    <div style="margin-top: 25px !important; margin-bottom: 10px !important">
        <h2>InfinitewellnessPK</h2>
    </div>
    <div style="margin-bottom: 25px !important;">
        <p>{{ $address }}</p>
    </div>
</center>

<table border="1">
    <thead>
        {{-- {{ dd($poses[0]->medicine_id) }} --}}
        <tr>
            <th>Product Name</th>
            <th>Manufacturer</th>
            <th>Sell QTY</th>
            <th>Return QTY</th>
            <th>Current QTY</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($medicines as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->product->manufacturer->company_name }}</td>
                <td>{{ $product->sell_qty }}</td>
                <td>{{ $product->return_qty }}</td>
                <td>{{ $product->total_quantity }}</td>
            </tr>

        @endforeach
    </tbody>
</table>

{{-- <table border="1">
    <thead>
        <tr>
            <th colspan="4">Product Name</th>
            <th colspan="4">Product QTY</th>
            <th colspan="4">Return QTY</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="4">{{ $product->product_name }}</th>
            <th colspan="4">{{ $totalQuantity }}</th>
            <th colspan="4">{{ $totalReturnQuantity }}</th>
        </tr>
    </tbody>

</table> --}}
<style>
    * {
        padding: 0px 10px 0px 10px !important;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    table tr,
    table th {
        margin: 10px !important;
        padding: 7px 17px 7px 17px !important;
    }

    .text-start {
        text-align: start;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.print();
    });
</script>
