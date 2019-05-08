<?php


namespace DenisKisel\Constructor\Services;


use DenisKisel\Constructor\Exceptions\CustomTranslationCommandException;
use DenisKisel\Helper\AStr;
use Illuminate\Support\Str;

class FieldsService
{
    public static function parse($input)
    {
        $fields = [];
        if (!empty($input)) {
            foreach (explode(',', $input) as $fieldForParse) {
                $isTranslation = false;
                if (Str::contains($fieldForParse, '[t]')) {
                    $isTranslation = true;
                    $fieldForParse = str_replace('[t]', '', $fieldForParse);
                }

                $migrationMethods = [];
                if (AStr::is('{*}', $fieldForParse)) {
                    $migrationMethodsForParse = explode('+', AStr::getContent('{*}', $fieldForParse));
                    AStr::rm('{*}', $fieldForParse);

                    foreach ($migrationMethodsForParse as $method) {
                        $methodParts = explode(':', $method);
                        $migrationMethods[] = [
                            'name' => $methodParts[0],
                            'value' => (isset($methodParts[1])) ? $methodParts[1] : null,
                        ];
                    }
                }

                $nameAndType = explode(':', $fieldForParse);

                if (count($nameAndType) < 2) {
                    throw new CustomTranslationCommandException('Wrong field: ' . $fieldForParse . '. Right pattern: name:type:length[null]');
                }

                $fields[] = [
                    'name' => $nameAndType[0],
                    'type' => $nameAndType[1],
                    'length' => (!empty($nameAndType[2])) ? $nameAndType[2] : null,
                    'migration_methods' => $migrationMethods,
                    'is_translation' => $isTranslation,
                ];
            }
        }

        return collect($fields);
    }
}