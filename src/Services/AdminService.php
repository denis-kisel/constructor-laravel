<?php


namespace DenisKisel\Constructor\Services;


use Illuminate\Support\Arr;
use DenisKisel\Helper\AStr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminService
{
    public static $adminControllerStub = __DIR__ . '/../../resources/custom/admin_controller.stub';

    public static function generateForm(Collection $fields)
    {
        $output = '';
        $availableAdminFieldTypes = include(__DIR__ . '/../../resources/fields/laravel_admin.php');
        foreach ($fields->toArray() as $field) {
            if (in_array('nullable', Arr::flatten($field), true)) {
                $output .= AStr::formatText("\$form->{$availableAdminFieldTypes[$field['type']]}('{$field['name']}', __('admin.{$field['name']}'));", 2);
            } else {
                $output .= AStr::formatText("\$form->{$availableAdminFieldTypes[$field['type']]}('{$field['name']}', __('admin.{$field['name']}'))->required();", 2);
            }
        }

        return $output;
    }

    public static function generateGrid(Collection $fields)
    {
        $output = '';
        foreach ($fields->toArray() as $field) {
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

    public static function makeController($modelClass, $fields, $replaces = null, $stub = null)
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
        $content = str_replace('{form}', self::generateForm($fields), $content);
        $content = str_replace('{grid}', self::generateGrid($fields), $content);

        if (!is_null($replaces)) {
            $content = str_replace($replaces[0], $replaces[1], $content);
        }

        file_put_contents($newControllerPath, $content);
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