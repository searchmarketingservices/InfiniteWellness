<div>
    {{ checkNumberFormat($row->followup_charge, strtoupper($row->currency_symbol ?? getCurrentCurrency())) }}
</div>

