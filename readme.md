# LaravelAdminReadySolution

This is package for generate model with laravel-admin controller by patterns

## Installation

Via Composer

``` bash
$ composer require denis-kisel/laravel-admin-ready-solution
```

## Usage

Create new page model with admin controller:
``` bash
# Command: larasol:page {model}
$ php artisan larasol:page Page

```

Create new product with categories(many to many) models with admin controllers:
``` bash
# Command: larasol:category-product {category_model} {product_model}
$ php artisan larasol:category-product Category Product

```
