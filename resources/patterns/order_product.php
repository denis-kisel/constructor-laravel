<?php
$output = [
    'order_id:integer',
    'product_id:integer',
    'name:string',
    'articul:string',
    'image:string',
    'price:integer',
    'discount:integer{nullable}',
    'discount_qty:integer{nullable}',
    'is_special:boolean{nullable}',
    'qty:integer',
];

return implode(',', $output);