<?php use_helper('Number'); ?>


<div id="charges_sum_amount">
    <h3>Sum of charges amount</h3>
    <div id="charges_sum_amount_page">For this page: <b><?php echo format_number($amounts['amount_page']); ?> CHF</b></div>
    <div id="charges_sum_amount_total">For all pages: <b><?php echo format_number($amounts['amount_total']); ?> CHF</b></div>
</div>

