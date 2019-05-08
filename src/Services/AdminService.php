<?php


namespace DenisKisel\Constructor\Services;


use Illuminate\Support\Arr;
use DenisKisel\Helper\AStr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminService
{
    public static $adminControllerStub = __DIR__ . '/../../resources/custom/admin_controller.stub';

    public static function generateForm(Collection $fields, $isTranslation = false)
    {
        $output = '';
        $availableAdminFieldTypes = include(__DIR__ . '/../../resources/fields/laravel_admin.php');
        foreach ($fields->where('is_translation', '=', $isTranslation)->toArray() as $field) {
            if (in_array('nullable', Arr::flatten($field), true)) {
                $output .= AStr::formatText("\$form->{$availableAdminFieldTypes[$field['type']]}('{$field['name']}', __('admin.{$field['name']}'));", 2);
            } else {
                $output .= AStr::formatText("\$form->{$availableAdminFieldTypes[$field['type']]}('{$field['name']}', __('admin.{$field['name']}'))->required();", 2);
            }
        }

        return $output;
    }

    public static function generateGrid(Collection $fields, $isTranslation = false)
    {
        $output = '';
        foreach ($fields->where('is_translation', '=', $isTranslation)->toArray() as $field) {
            if ($field['name'] == 'image') {
                $output .= <<<EOF
        \$grid->{$field['name']}( __('admin.{$field['name']}'))->display(function(\$image) {
            \$src = (\$image) ? SmartImage::cache(\$image, 100, 100) : '';
            return (\$src) ? "<img src='{\$src}'>" : '';
        });

EOF;
            } else {
                $output .= AStr::formatText("\$grid->{$field['name']}( __('admin.{$field['name']}'))->editable('text');", 2);
            }
        }
        return $output;
    }

    public static function makeController($modelClass, $form, $grid, $replaces = null, $stub = null)
    {
        $basenameModelClass = class_basename($modelClass);
        $controllerStubPath = (is_null($stub)) ? self::$adminControllerStub : $stub;
        $newControllerPath = app_path("Admin/Controllers/{$basenameModelClass}Controller.php");
        copy($controllerStubPath, $newControllerPath);

        $title = Str::snake($basenameModelClass);
        $title = str_replace('_', ' ', $title);
        $title = Str::title($title);

        $content = file_get_contents($newControllerPath);
        $content = str_replace('{basenameModelClass}', $basenameModelClass, $content);
        $content = str_replace('{controllerClass}', "{$basenameModelClass}Controller", $content);
        $content = str_replace('{modelClass}', "{$modelClass}", $content);
        $content = str_replace('{title}', $title, $content);
        $content = str_replace('{form}', $form, $content);
        $content = str_replace('{grid}', $grid, $content);

        if (!is_null($replaces)) {
            $content = str_replace($replaces[0], $replaces[1], $content);
        }

        file_put_contents($newControllerPath, $content);
    }

    public static function makeControllerTranslation($models, $fields)
    {
        foreach ($models as $model) {
            if ($model['template'] == 'controller') {
                self::makeController(
                    $model['class'],
                    self::generateForm($fields),
                    self::generateGrid($fields) . self::generateGrid($fields, true),
                    ['{translation_form}', self::generateForm($fields, true)],
                    __DIR__ . '/../../resources/custom_translation/controller.stub'
                );
            } else {
                self::makeController(
                    $model['class'],
                    self::generateForm($fields, true),
                    self::generateGrid($fields, true)
                );
            }
        }
    }

    public static function addRoute($basenameModel)
    {
        $routesPath = app_path('Admin/routes.php');

        $slug = Str::slug(Str::plural(Str::snake($basenameModel)), '-');
        $newRoute = "\$router->resource('{$slug}', '{$basenameModel}Controller');";

        $src = '], function (Router $router) {';
        $replace = <<<EOF
], function (Router \$router) {
    $newRoute
EOF;
        $content = file_get_contents($routesPath);

        if (Str::contains($content, "{$basenameModel}Controller")) {
            $output = "Admin route for {$basenameModel}Controller already exists!";
        } else {
            $content = str_replace($src, $replace, $content);
            file_put_contents($routesPath, $content);
            $output = "Admin route for {$basenameModel}Controller is created! Route: {$slug}";
        }

        return $output;
    }
}