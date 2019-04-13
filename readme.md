# LaravelAdminReadySolution

This is package for generate model with laravel-admin controller by patterns

## Installation

Via Composer

``` bash
$ composer require denis-kisel/laravel-admin-ready-solution
```

## Usage

Create new model with admin controller:
``` bash
# Command: larasol {pattern} {--model}
# Where {pattern} - page
# And {--model} - is optional (by default - Page)
$ php artisan larasol page 

#For example create new Post model
$ php artisan larasol page --model=Post
```
