# Constructor

This is package for generate migrations with models and/or [Laravel Admin](https://github.com/z-song/laravel-admin) controllers by patterns

Package bind(but can use only for one of each):  
* [Laravel Admin](https://github.com/z-song/laravel-admin)
* [Laravel Translatable](https://github.com/dimsav/laravel-translatable)
## Installation

Via Composer

``` bash
$ composer require denis-kisel/constructor
```

## Usage
###  Create Model With Empty Migration
Command: `construct:model ModelName` 

##### Example
``` bash
$ php artisan construct:model App\\Models\\Post 
```
#####  Output
* Model: `App\Models\Post`
* Migration: 

```php
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->timestamps();
});
```
###  Create Model With Fields
Command: `construct:model ModelName [options]`  
Option: `{--fields=}`  
Field signature: `name:type:length{extraMethod:paramValue}`  
Multi fields and extra methods must separate by comma `,`


##### Example
``` bash
$ php artisan construct:model App\\Models\\Post --fields=name:string:50,description:text{nullable},sort:integer{default:0},is_active:boolean{default:1}
```
#####  Output
* Model: `App\Models\Post`
* Migration:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name', 50);
    $table->text('description')->nullable();
    $table->integer('sort')->default(0);
    $table->boolean('is_active')->default(1);
    $table->timestamps();
});
```

### Create Model With Bind To Locale(Translation)
Command: `construct:modelt ModelName [options]`  
Option: `{--fields=}`  
Field signature: `name:type:length{extraMethod:paramValue}[t]`
Param `[t]` is optional and denotes `translation` field  
Multi fields and extra methods must separate by comma `,`


##### Example
``` bash
$ php artisan construct:modelt App\\Models\\Post --fields=name:string:50[t],description:text{nullable}[t],sort:integer{default:0},is_active:boolean{default:1}
```
#####  Output
* Models: `App\Models\Post`, `App\Models\PostTranslation`
* Migrations:

```php
// For Post
Schema::create('posts', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->integer('sort')->default('0');
    $table->boolean('is_active')->default('1');
    $table->timestamps();
});


// For PostTranslation
Schema::create('post_translations', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->integer('post_id')->unsigned();
    $table->string('locale')->index();
    $table->string('name', 50);
    $table->text('description')->nullable();
    $table->unique(['post_id','locale']);
    $table->timestamps();
});
```

### Create Model With Basic Page Fields
Command: `construct:page ModelName [options]`  

##### Example
``` bash
$ php artisan construct:page App\\Models\\Post
```
#####  Output
* Model: `App\Models\Post`
* Migration:

```php
Schema::create('pages', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('code')->nullable();
    $table->string('slug')->nullable();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('title')->nullable();
    $table->string('h1')->nullable();
    $table->text('keywords')->nullable();
    $table->text('meta_description')->nullable();
    $table->integer('sort')->default('0');
    $table->boolean('is_active')->default('1');
    $table->timestamps();
});
```

### Create Laravel-Admin Controller
Command: `construct:admin ModelName {--fields=}`  
Field signature: `name:type:length{extraMethod:paramValue}`  
Multi fields and extra methods must separate by comma `,`


##### Example
``` bash
$ php artisan construct:admin App\\Models\\Post --fields=name:string:50,description:text{nullable},sort:integer{default:0},is_active:boolean{default:1}
```
#####  Output
* Admin Controller: `App\Admin\Controllers\PostController`
* Contains:

```php
// Grid
protected function grid()
{
    $grid = new Grid(new Post);

    $grid->model()->orderBy('created_at', 'desc');

    $grid->id(__('admin.id'));
    $grid->name( __('admin.name'))->editable('text');
    $grid->description( __('admin.description'))->editable('text');
    $grid->sort( __('admin.sort'))->editable('text');
    $grid->is_active( __('admin.is_active'))->editable('select', ActiveHelper::editable());

    $grid->created_at(__('admin.created_at'));
    $grid->updated_at(__('admin.updated_at'));

    $grid->actions(function ($actions) {
        $actions->disableView();
    });

    return $grid;
}

// Form
protected function form()
{
    $form = new Form(new Post);

    $form->text('name', __('admin.name'))->required();
    $form->summernote('description', __('admin.description'));
    $form->number('sort', __('admin.sort'))->default('0')->required();
    $form->switch('is_active', __('admin.is_active'))->default('1')->required();

    return $form;
}
```

## Options
### Create Model With Fields
Command: `construct:modelt ModelName {--fields=}`  
Field signature: `name:type:length{extraMethod:paramValue}[t]`
Param `[t]` is optional and denotes `translation` field  
Multi fields and extra methods must separate by comma `,`

##### Example
``` bash
$ php artisan construct:model App\\Models\\Post --fields=name:string:50[t],description:text{nullable}[t],sort:integer{default:0},is_active:boolean{default:1}
```

### Create Model And Run Migration
Option: `{--m}` *(migration)*  

##### Example
``` bash
$ php artisan construct:model App\\Models\\Post --fields=name:string:50 --m
```

### Overwrite Exists Model Or/And Controller
Option: `{--i}` *(ignore)*  

##### Example
``` bash
$ php artisan construct:model App\\Models\\Post --fields=name:string:50 --i
```


### Create Model With Laravel-Admin Controller
Option: `{--a}` *(admin)*  

##### Example
``` bash
$ php artisan construct:model App\\Models\\Post --fields=name:string:50 --a
```


## Commands
### Create Model
Command: `construct:model [options]`

### Create Translatable Model(Bind With Locale)
Command: `construct:modelt [options]`

### Create Model With Basics Page Fields
Command: `construct:page [options]`
Basic Fields: `id`, `code`, `slug`, `name`, `description`, `title`, `h1`, `keywords`, `meta_description`, `sort`, `is_active`, `timestamps`

### Create Model With Basics Page Fields(Bind With Locale)
Command: `construct:paget [options]`
Basic Fields Page: `id`, `sort`, `is_active`, `timestamps`
Basic Fields PageTranslation: `code`, `slug`, `name`, `description`, `title`, `h1`, `keywords`, `meta_description`

### Create Model With Basics Page Fields
Command: `construct:page [options]`
Basic Fields: `id`, `code`, `slug`, `name`, `description`, `title`, `h1`, `keywords`, `meta_description`, `sort`, `is_active`, `timestamps`

### Create Laravel Admin Controller
Command: `construct:admin [options]`

### Create Translatable Laravel Admin Controller(Bind With Locale)
Command: `construct:admint [options]`

### Create Laravel Admin Controller With Basics Page Fields
Command: `construct:admin_page [options]`
Basic Fields: See `construct:page`

### Create Locale Model With Laravel Admin Controller
Command: `install:locale [options]`

##### Example
```bash
$ php artisan install:locale --a --m
```

## License
This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT)

## Contact
Developer: Denis Kisel
* Email: denis.kisel92@gmail.com
* Skype: live:denis.kisel92

