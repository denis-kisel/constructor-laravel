<?php
$output = [
    'code:string',
    'name:string',
    'image:string{nullable}',
    'description:text{nullable}',
    'sort:integer{default:0}',
    'is_active:boolean{default:1}'
];

return implode(',', $output);