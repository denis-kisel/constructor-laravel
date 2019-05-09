<?php
$output = [
    'code:string{nullable}[t]',
    'name:string[t]',
    'description:text{nullable}[t]',
    'title:string{nullable}[t]',
    'h1:string{nullable}[t]',
    'keywords:text{nullable}[t]',
    'meta_description:text{nullable}[t]',
    'sort:integer{default:0}',
    'is_active:boolean{default:1}'
];

return implode(',', $output);