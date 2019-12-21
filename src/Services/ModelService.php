<?php


namespace DenisKisel\Constructor\Services;


class ModelService
{
    public static function create($modelClass, $stub, $replacer = null)
    {
        $modelPath = base_path(lcfirst(str_replace('\\', '/', $modelClass)) . '.php');

        $basedir = pathinfo($modelPath, PATHINFO_DIRNAME);
        is_dir($basedir) || `mkdir -p {$basedir}`;

        copy($stub, $modelPath);

        $basenameClass = class_basename($modelClass);
        $namespace = str_replace('\\' . $basenameClass, '', $modelClass);

        $contents = file_get_contents($modelPath);
        $contents = str_replace('{namespace}', $namespace, $contents);
        $contents = str_replace('{class}', $basenameClass, $contents);

        if ($replacer) {
            $contents = str_replace($replacer[0], $replacer[1], $contents);
        }
        file_put_contents($modelPath, $contents);
    }
}
