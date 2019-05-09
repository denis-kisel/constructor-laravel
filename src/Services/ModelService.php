<?php


namespace DenisKisel\Constructor\Services;


use DenisKisel\Helper\AStr;

class ModelService
{
    public static function create($modelClass, $stub, $replacer = null)
    {
        $modelPath = base_path(lcfirst(str_replace('\\', '/', $modelClass)) . '.php');
        copy($stub, $modelPath);

        dump($modelPath);

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

    public static function generateTranslationAttributes($fields, $method = 'public $translatedAttributes')
    {
        $output = AStr::formatText("{$method} = [");
        $output .= AStr::formatText('\'locale\',', 2);
        foreach ($fields->where('is_translation', '=', 1)->toArray() as $item) {
            $output .= AStr::formatText("'{$item['name']}',", 2);
        }
        $output .= AStr::formatText('];', 1);
        return $output;
    }

    public static function generateTranslationFillable($fields)
    {
        return self::generateTranslationAttributes($fields, 'protected $fillable');
    }

    public static function generateTransesMethod($basenameClass)
    {
        return <<<EOF
    public function transes()
    {
        return \$this->hasMany({$basenameClass}::class);
    }
EOF;

    }
}