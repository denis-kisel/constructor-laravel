<?php
$output = [
    'delivery_id:integer{nullable}',
    'payment_id:integer{nullable}',
    'status_id:integer{nullable}',
    'delivery_address:text{nullable}',
    'delivery_price:integer{nullable}',
    'payment_diff:string{nullable}',
    'promo_name:string{nullable}',
    'promo_code:string{nullable}',
    'promo_discount:integer{nullable}',
    'total:integer',
    'comment:text{nullable}',
];

return implode(',', $output);