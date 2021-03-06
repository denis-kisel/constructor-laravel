<?php


namespace DenisKisel\Constructor\Services;


use DenisKisel\Constructor\Exceptions\MigrationException;
use DenisKisel\Helper\AStr;
use Illuminate\Support\Str;

class MigrationService
{
    public static function create($basenameModelName, $generatedFields, $stub, $replacer = [])
    {
        $table = Str::plural(Str::snake($basenameModelName));
        $migrationPath = database_path('migrations/' . date('Y_m_d_His') . "_create_{$table}_table.php");
        copy($stub, $migrationPath);

        $content = file_get_contents($migrationPath);
        $class = Str::studly($table);
        $content = str_replace('{class}', "Create{$class}Table", $content);
        $content = str_replace('{table}', $table, $content);
        $content = str_replace('{fields}', $generatedFields, $content);

        if ($replacer) {
            foreach ($replacer as $item) {
                $content = str_replace($item[0], $item[1], $content);
            }
        }

        file_put_contents($migrationPath, $content);
    }

    public static function generateMigrationFields(Array $fields)
    {
        $output = '';
        $availableFieldTypes = include(__DIR__ . '/../../resources/fields/migration.php');

        foreach ($fields as $field) {

            if (!in_array($field['type'], $availableFieldTypes)) {
                $textFinder = new \SimilarText\Finder($field['type'], $availableFieldTypes);

                throw new MigrationException("Not available field - {$field['type']}. Did you mean [{$textFinder->first()}]?");
            }

            $migrationMethods = self::makeMigrationMethods($field['migration_methods']);
            if (!is_null($field['length'])) {
                $output .= AStr::formatText("\$table->{$field['type']}('{$field['name']}', {$field['length']}){$migrationMethods};", 3);
            } else {
                $output .= AStr::formatText("\$table->{$field['type']}('{$field['name']}'){$migrationMethods};", 3);
            }
        }
        return $output;
    }

    public static function makeMigrationMethods($methods)
    {
        $output = '';
        if ($methods) {
            foreach ($methods as $method) {
                if (!is_null($method['value'])) {
                    $output .= "->{$method['name']}('{$method['value']}')";
                } else {
                    $output .= "->{$method['name']}()";
                }
            }
        }

        return $output;
    }
}
