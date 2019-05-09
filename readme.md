# Constructor

This is package for generate models and/or laravel-admin controllers by patterns

Package bind(but can use only for one of each):  
* [Laravel](https://github.com/laravel/laravel)
* [Laravel Admin](https://github.com/z-song/laravel-admin)
* [Laravel Translatable](https://github.com/dimsav/laravel-translatable)
## Installation

Via Composer

``` bash
$ composer require denis-kisel/constructor
```

## Usage

Create new model
``` bash
# Command: construct:model {model} {--fields=} {--m} {--a} {--i}

# {model} - must be with namespace(exp: App\\Models\\Page)
$ php artisan construct:model App\\Models\\Page


# {--fields} - custom fields by pattern: name:data_type:length{migration_methods}
# Where 'name' is field name of DB table, 'data_type' is type by migration methods(string|text|boolean .. eth)
# 'length' is optional param
$ php artisan construct:model App\\Models\\Page --fields=name:string:50

# Add more fields:
$ php artisan construct:model App\\Models\\Page --fields=name:string:50,description:text,sort:number


# {migration_methods} - additional migration methods. Optional param.
# {migration_methods} pattern: {method_name:method_param}. method_param is optional param
$ php artisan construct:model App\\Models\\Page --fields=name:string{nullable}

# Add more migration methods:
$ php artisan construct:model App\\Models\\Page --fields=name:string{nullable+default:0}


# {--m} - call migrate after create new model
$ php artisan construct:model App\\Models\\Page --fields=name:string:50 --m


# {--a} - call construct:admin by model. Generate admin controller.
$ php artisan construct:model App\\Models\\Page --fields=name:string:50 --a


# {--i} - ignore exists model. You can overwrite your model.
$ php artisan construct:model App\\Models\\Page --fields=name:string:50 --i

```

Create new model with translation(bind with locale)
``` bash
# Install locale table
$ php artisan install:locale --m

# Install locale table with admin controller
$ php artisan install:locale --a

# Command: construct:modelt {model} {--fields=} {--i} {--m} {--a}
$ php artisan construct:modelt App\\Models\\Page

# {--fields} - custom fields by pattern: name:data_type:length{migration_methods}[t]
# [t] - mark field as translation for bind locale. Optional param.
$ php artisan construct:modelt App\\Models\\Page --fields=name:string:50[t],description:text[t],sort:number
```

Create new model with basic set fields for typical page
``` bash
# Command: construct:page {model} {--fields=} {--a} {--i} {--m}
$ php artisan construct:page App\\Models\\Page

# Migration file:
<?php
...
    $table->bigIncrements('id');
    $table->string('code')->nullable();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('title')->nullable();
    $table->string('h1')->nullable();
    $table->text('keywords')->nullable();
    $table->text('meta_description')->nullable();
    $table->integer('sort')->default('0');
    $table->boolean('is_active')->default('1');
    $table->timestamps();
...
?>


$ php artisan construct:page App\\Models\\Page --fields=additional_description:text{nullable}

# Migration file:
<?php
...
    $table->bigIncrements('id');
    $table->string('code')->nullable();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('title')->nullable();
    $table->string('h1')->nullable();
    $table->text('keywords')->nullable();
    $table->text('meta_description')->nullable();
    $table->text('additional_description')->nullable();
    $table->integer('sort')->default('0');
    $table->boolean('is_active')->default('1');
    $table->timestamps();
...
?>


# Also available generate typical translation page
$ php artisan construct:paget App\\Models\\Page --fields=additional_description:text{nullable}[t]
```

## Available commands
* construct:admin {model} {--fields=} {--i}
* construct:admin_page {model} {--fields=}
* construct:admint {model} {--fields=} {--i}
* install:admin_locale
* install:locale {--m} {--i} {--a}
* construct:model {model} {--fields=} {--i} {--m} {--a}
* construct:modelt {model} {--fields=} {--i} {--m} {--a}
* construct:page {model} {--fields=} {--a} {--i} {--m}
* construct:paget {model} {--fields=} {--a} {--i} {--m}