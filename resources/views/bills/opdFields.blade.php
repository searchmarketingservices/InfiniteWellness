<div class="row">
    {{ Form::hidden('currency_symbol', getCurrentCurrency(), ['class' => 'currencySymbol']) }}
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('patient_opd_id', 'Patient OPD ID' . ':', ['class' => 'form-label']) }}
        <span class="required"></span>

        {{ Form::select('patient_opd_id', $data['opd'], null, ['class' => 'form-select', 'id' => 'patientOPDId', 'placeholder' => 'Select OPD Id', 'data-control' => 'select2', 'required']) }}

    </div>
    {{ Form::hidden('patient_admission_id', null, ['id' => 'pAdmissionId']) }}
    {{ Form::hidden('patient_id', null, ['id' => 'billsPatientId']) }}
    @if (isset($bill))
        <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
            {{ Form::label('bill_date', __('messages.bill.bill_date') . ':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('bill_date', null, ['class' => getLoggedInUser()->thememode ? 'bg-light form-control' : 'bg-white form-control', 'id' => 'editBillDate', 'autocomplete' => 'off']) }}
        </div>
    @else
        <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
            {{ Form::label('bill_date', __('messages.bill.bill_date') . ':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('bill_date', null, ['class' => getLoggedInUser()->thememode ? 'bg-light form-control' : 'bg-white form-control', 'id' => 'bill_date', 'autocomplete' => 'off']) }}
        </div>
    @endif
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5 myclass">
        {{ Form::label('name', __('messages.case.patient') . ':', ['class' => 'form-label']) }}
        {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'readonly']) }}
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('email', __('messages.bill.patient_email') . ':', ['class' => 'form-label']) }}
        {{ Form::text('email', null, ['class' => 'form-control', 'id' => 'userEmail', 'readonly']) }}
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('phone', __('messages.bill.patient_cell_no') . ':', ['class' => 'form-label']) }}
        {{ Form::text('phone', null, ['class' => 'form-control', 'id' => 'userPhone', 'readonly']) }}
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('gender', __('messages.bill.patient_gender') . ':', ['class' => 'form-label']) }}
        <br>
        <div class="d-flex align-items-center mt-3">
            <div class="form-check me-10 mb-0">
                {{ Form::radio('gender', '0', true, ['class' => 'form-check-input', 'tabindex' => '6', 'id' => 'genderMale']) }}
                &nbsp;
                <label class="form-check-label" for="genderMale">{{ __('messages.user.male') }}</label>
            </div>
            <div class="form-check mb-0">
                {{ Form::radio('gender', '1', false, ['class' => 'form-check-input', 'tabindex' => '7', 'id' => 'genderFemale']) }}
                <label class="form-check-label" for="genderFemale">{{ __('messages.user.female') }}</label>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('dob', __('messages.bill.patient_dob') . ':', ['class' => 'form-label']) }}
        {{ Form::text('dob', null, ['class' => 'form-control', 'id' => 'dob', 'readonly']) }}
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('doctor_id', __('messages.case.doctor') . ':', ['class' => 'form-label']) }}
        {{ Form::text('doctor_id', null, ['class' => 'form-control', 'id' => 'billDoctorId', 'readonly']) }}
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('admission_date', 'OBD Date' . ':', ['class' => 'form-label']) }}
        {{ Form::text('admission_date', null, ['class' => 'form-control', 'id' => 'opdDate', 'readonly']) }}
    </div>

    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('charges', 'Standard Charges' . ':', ['class' => 'form-label']) }}
        {{ Form::text('charges', null, ['class' => 'form-control', 'id' => 'opdCharge', 'readonly']) }}
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('followUpCharge', 'Follow Up Charges' . ':', ['class' => 'form-label']) }}
        {{ Form::text('followUpCharge', null, ['class' => 'form-control', 'id' => 'followUpCharge', 'readonly']) }}
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('payment', 'Mode of Payment' . ':', ['class' => 'form-label']) }}
        <select name="payment_type" class="form-control payment_type" required>
            @foreach ($bills as $key => $bill)
                <option value="{{ $key }}">{{ $bill }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-12 mb-5">
        {{ Form::label('discount_fetch', 'Available Discount' . ':', ['class' => 'form-label']) }}
        <select name="discount_fetch" class="form-control discount_fetch" required>
            @foreach ($data['discount'] as $discount)
                <option value="{{ $discount->amount_per }}" id="dynamic_discount" onchange="discount_amount()">{{ $discount->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="com-sm-12">
    <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-end mb-4">
        {{-- <button type="button" class="btn btn-primary text-star" id="addBillItem"> {{ __('messages.invoice.add') }}</button> --}}

        <button type="button" id="add" onclick="Addmore()" class="btn btn-primary">Add</button>
    </div>
    <div class="table-responsive-sm">
        <table class="table table-striped" id="billTbl">
            <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="text-center">#</th>
                    <th class="required">{{ __('messages.bill.item_name') }}</th>
                    {{-- <th class="required">{{ __('messages.bill.qty') }}</th> --}}
                    <th class="required">{{ __('messages.bill.price') }}</th>
                    {{-- <th class="text-right">Discount %</th> --}}
                    <th class="text-right">{{ __('messages.bill.amount') }}</th>
                    <th class="text-center">
                        {{ __('messages.common.action') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bill-item-container text-gray-600 fw-bold" id="tableBody">
                @if (isset($data['bill']))
                    @foreach ($bill->billItems as $billItem)
                        <tr>
                            <td class="text-center item-number">{{ $loop->iteration }}</td>
                            <td class="table__item-desc">
                                {{ Form::text('item_name[]', $billItem->item_name, ['class' => 'form-control itemName', 'required']) }}
                            </td>
                            <td>
                                {{ Form::text('price[]', number_format($billItem->price), ['class' => 'form-control price-input price', 'required']) }}
                            </td>
                            <td class="amount text-right itemTotal">
                                <input type="hidden" id="itemTotals"
                                    value="{{ number_format($billItem->amount) }}">{{ number_format($billItem->amount) }}
                            </td>
                            <td class="text-center">
                                <i class="fa fa-trash text-danger delete-bill-add-item pointer"></i>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="itemList" id="itemList">
                        <td class="text-center item-number">1</td>
                        <td class="table__item-desc">
                            {{ Form::text('item_name[]', null, ['class' => 'form-control itemName', 'required']) }}
                        </td>
                        <td>
                            {{-- {{ Form::text('price[]', null, ['class' => 'form-control price-input price','required', 'onkeypress' => 'calculateTotal()']) }} --}}
                            <input name="price[]" type="text" class="form-control price" required
                                onkeyup="calculateTotal()">

                        </td>
                        {{-- <td> --}}
                        {{-- <input type="number" id="discount"   class="form-control"> --}}
                        {{-- {{ Form::number('diccount', null, ['class' => 'form-control','required','id' => 'diccount']) }} --}}
                        {{-- </td> --}}
                        <td class="amount text-right itemTotal" id='amountTotal0'>
                            0.00
                        </td>
                        <td class="text-center">
                            <i class="fa fa-trash text-danger delete-invoice-item pointer"></i>
                        </td>
                        <td class="table__qty">
                            {{ Form::hidden('qty[]', 1, ['class' => 'form-control qty quantity', 'required']) }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="col-lg-4 col-md-8 col-sm-8 float-right p-0">
        <table class="w-100">
            <tbody class="bill-item-footer">
                <tr>
                    <td class="text-right pe-4">
                        <label class="form-label text-right" for="discount">Total</label>
                        <input readonly type="text" id="total" class="form-control">
                    </td>
                    <td class="text-right pe-4">
                        <label class="form-label text-right" for="discount">Advance</label>
                        <input name="advance_amount" readonly type="text" class="form-control" id="advance_amount"
                            onkeyup="calculateTotal()">
                        <input name="advance_amount" type="hidden" id="advance_amount2">
                    </td>
                    <td class="text-right pe-4">
                        <label class="form-label text-right" for="discount">Discount %</label>
                        <input name="discount_amount" type="text" class="form-control" id="discount"
                            onkeyup="calculateTotal()">
                        <input name="total_amount" type="hidden" id="total_amounts">

                    </td>
                    <td class="text-right">
                        <label class="form-label text-right" for="discount">Net Total</label>
                        <input type="number" readonly step="any" class="form-control" class="form-control"
                            id="totalPrices">
                        <input type="hidden" readonly step="any" class="form-control" class="form-control"
                            id="totalPrices2">
                    </td>
                    {{-- <td class="form-label text-right">{{ __('messages.bill.total_amount').(':') }}</td>
                <td class="text-right">
                    <span id="totalPrice" class="price">{{ isset($bill) ? getCurrencySymbol() . '' . number_format($bill->amount,2) : getCurrencySymbol() . '' . 0 }}</span>
                    <input type="hidden" id="totalPrices" >
                </td> --}}
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Total Amount Field -->
{{-- {{ Form::hidden('total_amount', null, ['class' => 'form-control', 'id' => 'totalAmount']) }} --}}

<!-- Submit Field -->
<div class="float-end">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2', 'id' => 'billSave']) }}
    <a href="{{ route('bills.index') }}" class="btn btn-secondary">{{ __('messages.common.cancel') }}</a>
</div>

<script>
    var input2 = document.getElementById('patientOPDId');
    input2.onchange = function() {
        var selectedText = input.value;
        let txt = input.options[input.selectedIndex].innerHTML;
        console.log('Selected Text:', selectedText);

        $.ajax({
            url: '/bills/opd/getPatient',
            method: 'GET',
            data: {
                patientID: selectedText,
                opdID: txt
            },
            success: function(response) {
                // Handle the successful response here
                let data = response;
                console.log('Response:', data);
                console.log(data['service_id']);

                if (data['service_id'] != null) {
                    var service = data['service_id'];
                    var lastRowNumber = $("#tableBody tr.itemList").length;
                    service = JSON.parse(service);
                    $.each(service, function(index, item) {
                        $("#tableBody").append(`
                        <tr id="billrow${lastRowNumber + index}" class="itemList">
                            <td class="text-center item-number">${lastRowNumber + index + 1}</td>
                            <td class="table__item-desc">
                                <input type="text" name="item_name[]" readonly class="form-control itemName" required value="${item.service}">
                            </td>
                            <td>
                                <input name="price[]" type="text" readonly value="${item.amount}" onkeyup="calculateTotal()" class="form-control price" required>
                            </td>
                            <td class="amount text-right itemTotal" id='amountTotal0'>
                                ${item.amount}
                            </td>
                            <td class="table__qty">
                            {{ Form::hidden('qty[]', 1, ['class' => 'form-control qty quantity', 'required']) }}
                        </td>
                        </tr>
                        `);
                    });
                    calculateTotal();

                }

                document.getElementById('billsPatientId').value = selectedText;
                document.getElementById('pAdmissionId').value = txt;
                document.getElementById('name').value = data['first_name'] + " " + data['last_name'];
                document.getElementById('userEmail').value = data['email'];
                document.getElementById('userPhone').value = data['phone'];
                document.getElementById('dob').value = data['dob'];
                document.getElementById('billDoctorId').value = data['doctor']["first_name"] + " " +
                    data['doctor']["last_name"];
                document.getElementById('opdDate').value = data['created_at'];
                // document.getElementById('totalPrice').innerHTML = "Rs " + data['charges'];
                if (data['charges'] != null) {
                    document.getElementById('followUpCharge').value = '';
                    document.getElementById('opdCharge').value = data['charges'];
                    document.getElementById('total').value = data['charges'];
                    document.getElementById('totalPrices').value = data['charges'] - data[
                        'advance_amount'];
                    document.getElementById('totalPrices2').value = data['charges'] - data[
                        'advance_amount'];
                } else {
                    document.getElementById('opdCharge').value = '';
                    document.getElementById('followUpCharge').value = data['followup_charge'];
                    document.getElementById('total').value = data['followup_charge'];
                    document.getElementById('totalPrices').value = data['followup_charge'] - data[
                        'advance_amount'];
                    document.getElementById('totalPrices2').value = data['followup_charge'] - data[
                        'advance_amount'];
                }
                document.getElementById('advance_amount').value = data['advance_amount'];
                document.getElementById('advance_amount2').value = data['advance_amount'];




                document.getElementById('discount').addEventListener("keyup", () => {

                    var diccount = document.getElementById('discount').value;
                    var totalPrices = document.getElementById('totalPrices2').value;
                    var discount_amount = (diccount * totalPrices) / 100;
                    $('#totalPrices').val(totalPrices - discount_amount - data[
                        'advance_amount']);
                    $('#totalPrices2').val(totalPrices - discount_amount - data[
                        'advance_amount']);

                })


                if (data['charges'] != null) {
                    document.getElementsByClassName('itemName')[0].value = "OPD";
                } else if (data['followup_charge'] != null) {
                    document.getElementsByClassName('itemName')[0].value = "OPD Follow Up";
                }
                // document.getElementsByClassName('quantity')[0].value = "1";
                if (data['charges'] != null) {
                    document.getElementsByClassName('price')[0].value = data['charges'];
                    document.getElementsByClassName('itemTotal')[0].innerHTML = data['charges'];
                } else {
                    document.getElementsByClassName('price')[0].value = data['followup_charge'];
                    document.getElementsByClassName('itemTotal')[0].innerHTML = data['followup_charge'];
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors that occurred during the request
                console.error('Error:', error);
            }
        });
    };
    // discount_amount fetch
    function discount_amount() {
        var paymentTypeDropdown = document.querySelector('.discount_fetch');

        paymentTypeDropdown.addEventListener('change', function() {
            var selectedValue = paymentTypeDropdown.value;
            console.log('Selected Payment Type Value: ' + selectedValue);

            // Append the selected value to the discount input field
            var discountInput = document.getElementById('discount');
            discountInput.value = selectedValue;

            // Optionally, recalculate the total after updating the discount
            calculateTotal();
        });
    }

    $(document).ready(function() {
        discount_amount();
        $("#patientOPDId").change(function() {
            $("#tableBody tr.itemList:not(:first-child)").remove();
        });
    })
    var input = document.getElementById('patientOPDId');

    input.onchange = function() {
        var selectedText = input.value;
        let txt = input.options[input.selectedIndex].innerHTML;
        console.log('Selected Texts:', selectedText);
        const opid =txt.split(" ");
        console.log("sdf",opid[0]);

        $.ajax({
    url: '/bills/opd/getPatient',
    method: 'GET',
    data: {
        patientID: selectedText,
        opdID: opid[0]
    },
    success: function(response) {
        // Handle the successful response here
        let data = response;
        console.log('Response:', data);
        console.log('gender : ', data['gender']);

        // Reset both radio buttons
        document.getElementById('genderMale').checked = false;
        document.getElementById('genderFemale').checked = false;

        // Set the correct radio button based on the gender
        if (data['gender'] == 0) {
            document.getElementById('genderMale').checked = true;
        } else if (data['gender'] == 1) {
            document.getElementById('genderFemale').checked = true;
        }

        // Continue handling other data
        document.getElementById('billsPatientId').value = selectedText;
        document.getElementById('pAdmissionId').value = txt;
        document.getElementById('name').value = data['first_name'] + " " + data['last_name'];
        document.getElementById('userEmail').value = data['email'];
        document.getElementById('userPhone').value = data['phone'];
        document.getElementById('dob').value = data['dob'];
        document.getElementById('billDoctorId').value = data['doctor']["first_name"] + " " +
            data['doctor']["last_name"];
        document.getElementById('opdDate').value = data['created_at'];

        if (data['charges'] != null) {
            document.getElementById('followUpCharge').value = '';
            document.getElementById('opdCharge').value = data['charges'];
            document.getElementById('total').value = data['charges'];
            document.getElementById('totalPrices').value = data['charges'] - data['advance_amount'];
            document.getElementById('totalPrices2').value = data['charges'] - data['advance_amount'];
        } else {
            document.getElementById('opdCharge').value = '';
            document.getElementById('followUpCharge').value = data['followup_charge'];
            document.getElementById('total').value = data['followup_charge'];
            document.getElementById('totalPrices').value = data['followup_charge'] - data['advance_amount'];
            document.getElementById('totalPrices2').value = data['followup_charge'] - data['advance_amount'];
        }

        if (data['service_id'] != null) {
            var service = data['service_id'];
            var lastRowNumber = $("#tableBody tr.itemList").length;
            service = JSON.parse(service);
            $.each(service, function(index, item) {
                $("#tableBody").append(`
                <tr id="billrow${lastRowNumber + index}" class="itemList">
                    <td class="text-center item-number">${lastRowNumber + index + 1}</td>
                    <td class="table__item-desc">
                        <input type="text" name="item_name[]" readonly class="form-control itemName" required value="${item.service}">
                    </td>
                    <td>
                        <input name="price[]" type="text" readonly value="${item.amount}" onkeyup="calculateTotal()" class="form-control price" required>
                    </td>
                    <td class="amount text-right itemTotal" id='amountTotal0'>
                        ${item.amount}
                    </td>
                    <td class="table__qty">
                        {{ Form::hidden('qty[]', 1, ['class' => 'form-control qty quantity', 'required']) }}
                    </td>
                </tr>
                `);
            });
            calculateTotal();
        }
    }
});

    };


    // function discount(){

    // }

    function calculateTotal() {
        setTimeout(function() {

            let itemTotal = document.getElementsByClassName('price');
            let totalAmount = 0.00;
            for (let i = 0; i < itemTotal.length; i++) {
                // console.log('Item Total:  ', itemTotal[i].value);
                totalAmount += parseFloat(itemTotal[i].value);
                // console.log('Total Amount:  ', totalAmount);
            }

            let dis = document.getElementById('discount');
            let totalPrices = document.getElementById('totalPrices');
            let total_amounts = document.getElementById('total_amounts');
            let advance_amount = document.getElementById('advance_amount2');
            let total = document.getElementById('total');
            console.log('Total :  ', total.value);


            if (dis.value.length == 0) {
                dis.value = 0.00;
            }
            if (advance_amount.value.length == 0) {
                advance_amount.value = 0.00;
            }
            var amounts = totalAmount - parseFloat(dis.value * totalAmount / 100) - parseFloat(advance_amount.value);
            // console.log('Ha' +  amounts);
            totalPrices.value = amounts;
            total_amounts.value = amounts;
            total.value = amounts;
            // console.log('Ha' +  amounts);
        }, 100);
    }


    function Addmore() {
        var tableRow = document.getElementsByClassName('itemList');
        var a = tableRow.length;
        $('.bill-item-container').append(`
            <tr id="billrow${a}" class="itemList">
                <td class="text-center item-number">${a + 1}</td>
                    <td class="table__item-desc">
                        {{ Form::text('item_name[]', null, ['class' => 'form-control itemName', 'required']) }}
                    </td>
                    <td>

                        <input name="price[]" type="text" class="form-control price" required onkeyup="calculateTotal()">
                    </td>
                    <td class="amount text-right itemTotal">
                        0.00
                    </td>
                    <td class="text-center">
                        <i class="fa fa-trash text-danger delete-invoice-item pointer" ></i>
                    </td>
                    <td class="table__qty">
                        {{ Form::hidden('qty[]', 1, ['class' => 'form-control qty quantity', 'required']) }}
                    </td>
            </tr>
            `);

    }

</script>
<style>
    #billTbl tr>*:nth-child(3) {
        /* display: none; */
    }
</style>
