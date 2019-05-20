<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\FieldsService;
use DenisKisel\Constructor\Services\ModelService;
use DenisKisel\Helper\AStr;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModelTranslationCommand extends Command
{
    /**
     * Example: construct:modelt App\\Models\\Page --fields=name:string[t],description:text{nullable}[t],is_active:boolean{nullable+default:1}
     */
    protected $signature = 'construct:modelt {model} {--fields=} {--i} {--m} {--a} {--mig_replacer=} {--mig_stub=} {--model_replacer=} {--model_stub=}';
    protected $description = 'Constructor for translation models';


    public function handle()
    {
        $this->callModels();
        $this->callAdmins();
    }

    //STACK
    protected function callModels()
    {
        foreach ($this->models() as $model) {
            $this->call('construct:model', [
                'model' => $model['class'],
                '--fields' => $this->getStringFieldsByType($model['template'] == 'translation_model'),
                '--i' => $this->option('i'),
                '--m' => $this->option('m'),
                '--mig_stub' => $model['mig_stub'],
                '--mig_replacer' => $this->migrationReplacer(),
                '--model_stub' => $model['model_stub'],
                '--model_replacer' => $this->modelReplacer()
            ]);
            sleep(1);
        }
    }

    protected function callAdmins()
    {
        if ($this->option('a')) {
            $this->call('construct:admint', [
                'model' => $this->argument('model'),
                '--fields' => $this->stringFields(),
                '--i' => $this->option('i')
            ]);
        }
    }

    //HELPERS
    protected function models()
    {
        return [
            [
                'class' => $this->argument('model'),
                'basename_class' => $this->baseNameModelClass(),
                'template' => 'model',
                'mig_stub' => $this->migrationStub()['m'],
                'model_stub' => $this->modelStub()['m']
            ],
            [
                'class' => $this->translationModelClass(),
                'basename_class' => $this->translationBaseModelClass(),
                'template' => 'translation_model',
                'mig_stub' => $this->migrationStub()['mt'],
                'model_stub' => $this->modelStub()['mt']
            ]
        ];
    }

    protected function migrationStub()
    {
        if ($this->option('mig_stub')) {
            return $this->option('mig_stub');
        }
        return [
            'm' => __DIR__ . '/../../resources/custom/migration.stub',
            'mt' => __DIR__ . '/../../resources/custom_translation/migration.stub',
        ];
    }

    protected function modelStub()
    {
        if ($this->option('model_stub')) {
            return $this->option('model_stub');
        }
        return [
            'm' => __DIR__ . '/../../resources/custom_translation/model.stub',
            'mt' => __DIR__ . '/../../resources/custom_translation/model_translation.stub',
        ];
    }

    protected function migrationReplacer()
    {
        if ($this->option('mig_replacer')) {
            return $this->option('mig_replacer');
        }
        return json_encode([
            '{belongTableId}',
            Str::snake($this->baseNameModelClass()) . '_id'
        ]);
    }

    protected function modelReplacer()
    {
        if ($this->option('model_replacer')) {
            return $this->option('model_replacer');
        }
        $fields = $this->collectionFields()->where('is_translation', '=', true)->toArray();
        $fieldsNames = [];
        foreach ($fields as $field) {
            $fieldsNames[] = '\'' . $field['name'] . '\'';
        }

        return json_encode([
            ['{fillable}', '{translation_model}'],
            [
                implode(', ', $fieldsNames),
                $this->translationBaseModelClass()
            ]
        ]);
    }

    protected function getStringFieldsByType($isTranslation = false)
    {
        $output = [];
        if ($this->stringFields()) {
            foreach (explode(',', $this->stringFields()) as $item) {
                if (
                    !Str::contains($item, '[t]') && !$isTranslation
                    || Str::contains($item, '[t]') && $isTranslation
                ) {
                    $output[] = $item;
                }
            }
        }

        return implode(',', $output);
    }

    protected function baseNameModelClass()
    {
        return class_basename($this->argument('model'));
    }

    protected function translationModelClass()
    {
        return $this->argument('model') . 'Translation';
    }

    protected function translationBaseModelClass()
    {
        return class_basename($this->translationModelClass());
    }

    protected function stringFields()
    {
        return $this->option('fields');
    }

    protected function collectionFields()
    {
        return FieldsService::parse($this->stringFields());
    }
}
