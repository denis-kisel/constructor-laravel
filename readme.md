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

##Demo
#####  Create simple model with migration
``` bash
$ construct:model App\\Models\\Post --fields=name:string:50,description:text{nullable},sort:integer{default:0},is_active:boolean{default:1}

# Output
# Created Post model: proj/app/Models/Post.php
# Created Post model migration with contents

<?php
...

Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name', 50);
    $table->text('description')->nullable();
    $table->integer('sort')->default(0);
    $table->boolean('is_active')->default(1);
    $table->timestamps();
});

...

```
#####  Create simple model with migration and laravel-admin controller
``` bash
$ construct:model App\\Models\\Post --fields=name:string:50,description:text{nullable},sort:integer{default:0},is_active:boolean{default:1} --a

# Output
# Created Post model: proj/app/Models/Post.php
# Created Post model migration with contents

<?php
...

Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name', 50);
    $table->text('description')->nullable();
    $table->integer('sort')->default(0);
    $table->boolean('is_active')->default(1);
    $table->timestamps();
});

...


# Created new resources route of laravel-admin: admin/posts
# Created PostController of laravel-admin: proj/app/Admin/Controllers/PostController.php with contents:

<?php
...

protected function grid()
{
    $grid = new Grid(new Post);

    $grid->model()->orderBy('created_at', 'desc');

    $grid->id(__('admin.id'));
   
    $grid->name(__('admin.name'))->editable('text');
    $grid->sort(__('admin.sort'))->editable('text');
    $grid->is_active(__('admin.is_active'))->editable('select', ActiveHelper::editable());
    $grid->created_at(__('admin.created_at'));
    $grid->updated_at(__('admin.updated_at'));

    $grid->actions(function ($actions) {
        $actions->disableView();
    });

    return $grid;
}

...

protected function form()
{
    $form = new Form(new Post);
    $form->text('name', __('admin.name'))->required();
    $form->summernote('description', __('admin.description'));
    $form->number('sort', __('admin.sort'))->default(0);
    $form->switch('is_active', __('admin.is_active'))->default(1);
    return $form;
}

...

```

## Docs
### Available admin commands
| Command | Description |
| --- | --- |
| `construct:admin {model} {--fields=} {--i}` | Construct laravel-admin controller |
| `construct:admint {model} {--fields=} {--i}` | Construct laravel-admin controller with bind to locale(translation) |
| `construct:admin_page {model} {--fields=} {--i}` | Construct laravel-admin controller with basic page fields |


### Available install commands
| Command | Description |
| --- | --- |
| `install:locale {--m} {--i} {--a}` | Install locale model with optional install controller for laravel-admin |
| `install:admin_locale` | Install locale controller for laravel-admin |


### Available model commands
| Command | Description |
| --- | --- |
| `construct:model {model} {--fields=} {--i} {--m} {--a}` | Construct model |
| `construct:modelt {model} {--fields=} {--i} {--m} {--a}` | Construct model with bind to locale(translation) |
| `construct:page {model} {--fields=} {--a} {--i} {--m}` | Construct model with basic page fields |
| `construct:paget {model} {--fields=} {--a} {--i} {--m}` | Construct model with basic page fields with bind to locale(translation) |


### Options
| Option | Description |
| --- | --- |
| `{model}` | Model name. Must be with *namespace* |
| `{--fields=}` | Custom fields by pattern: *name:data_type:length{migration_methods}\[t\]*. Separate by `,` |
| `{--pattern_path=}` | Path to file with custom fields by --fields pattern |
| `{--i}` | Ignore exists model or controller |
| `{--m}` | Make model and migration with `migrate` command |
| `{--a}` | Construct laravel-admin controller |


### Fields pattern
*name:data_type:length{migration_methods}\[t\]*

| Option | Description |
| --- | --- |
| `name` | Column name |
| `data_type` | Data type of DB(`string`, `text`, `json` ... ) |
| `length` | Column field length. Optional param |
| `{migration_methods}` | Migration methods by pattern *{method_name:method_param}*. Separate by `+`. Optional param |
| `[t]` | Mark field as translation(for bind to locale). Optional param |


### Migration methods pattern
*{method_name:method_param}*

| Option | Description |
| --- | --- |
| `method_name` | Method name. Exp: nullable -> nullable() |
| `method_param` | Method value. Exp: default:1 -> default(1) |

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