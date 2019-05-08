<?php


namespace DenisKisel\Constructor\Services;


use DenisKisel\Helper\AStr;

class ModelService
{
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