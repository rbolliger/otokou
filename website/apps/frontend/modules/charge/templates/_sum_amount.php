<?php use_helper('Number'); ?>


<div id="charges_sum_amount">
    <h4>Total amounts</h4>
    <div id="charges_sum_amount_page">This page: <b><?php echo format_number($amounts['amount_page']); ?> CHF</b></div>
    <div id="charges_sum_amount_total">All pages: <b><?php echo format_number($amounts['amount_total']); ?> CHF</b></div>
</div>

