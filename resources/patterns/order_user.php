<?php
$output = [
    'order_id:integer',
    'user_id:integer{nullable}',
    'name:string{nullable}',
    'email:string{nullable}',
    'telephone:string{nullable}',
    'address:string{nullable}',
];

return implode(',', $output);