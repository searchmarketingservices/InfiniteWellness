<div>
    {{ checkNumberFormat($row->total_amount, strtoupper($row->currency_symbol ?? getCurrentCurrency())) }}
</div>

