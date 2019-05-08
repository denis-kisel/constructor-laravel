<?php


namespace DenisKisel\Constructor\Services;


use DenisKisel\Helper\AStr;

class ModelService
{
    public static function generateTranslationAttributes($fields)
    {
        $output = AStr::formatText('public $translatedAttributes = [');
        foreach ($fields->where('is_translation', '=', 1)->toArray() as $item) {
            $output .= AStr::formatText("'{$item['name']}',", 2);
        }
        $output .= AStr::formatText('];', 1);
        return $output;
    }

    public static function generateTranslationFillable($fields)
    {
        $output = AStr::formatText('protected $fillable = [');
        foreach ($fields->where('is_translation', '=', 1)->toArray() as $item) {
            $output .= AStr::formatText("'{$item['name']}',", 2);
        }
        $output .= AStr::formatText('];', 1);
        return $output;
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