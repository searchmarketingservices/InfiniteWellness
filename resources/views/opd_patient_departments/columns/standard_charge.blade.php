<div>
    {{ checkNumberFormat($row->standard_charge, strtoupper($row->currency_symbol ?? getCurrentCurrency())) }}
</div>

