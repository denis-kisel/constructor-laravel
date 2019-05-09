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
     * The name and signature of the console command.
     * fields: field_name:data_type:length{migration_methods}
     * {type} - string|integer|text
     *
     * Example: construct:modelt App\\Models\\Page name:string[t],description:text{nullable}[t],is_active:boolean{nullable+default:1}
     *
     * @var string
     */
    protected $signature = 'construct:modelt {model} {--fields=} {--i} {--m} {--a}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Constructor for translation models';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->callModels();
        $this->bindModels();
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
                '--mig_stub' => $model['stub'],
                '--mig_replacer' => json_encode($this->replacer())
            ]);
            sleep(1);
        }
    }

    protected function bindModels()
    {
        //MODEL
        $pathModelClass = AStr::pathByClass($this->argument('model'));
        $contents = file_get_contents($pathModelClass);
        $contents = AStr::append(
            'use Illuminate\Database\Eloquent\Model;',
            'use Dimsav\Translatable\Translatable;',
            $contents
        );

        $contents = AStr::prepend('}', 'use Translatable;', $contents, 1);
        $contents = AStr::prepend('}', ModelService::generateTranslationAttributes($this->arrayFields()), $contents, 1);
        $contents = AStr::prepend('}', ModelService::generateTransesMethod($this->translationBaseModelClass()), $contents);
        file_put_contents($pathModelClass, $contents);

        //TRANSLATION MODEL
        $pathTranslationModelClass = AStr::pathByClass($this->translationModelClass());
        $contents = file_get_contents($pathTranslationModelClass);
        $contents = AStr::prepend('}', ModelService::generateTranslationFillable($this->arrayFields()), $contents, 1);
        file_put_contents($pathTranslationModelClass, $contents);
    }

    protected function callAdmins()
    {
        if ($this->option('a')) {
            $this->call('construct:admint', [
                'model' => $this->argument('model'),
                'fields' => $this->stringFields(),
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
                'stub' => __DIR__ . '/../../resources/custom/migration.stub'
            ],
            [
                'class' => $this->translationModelClass(),
                'basename_class' => $this->translationBaseModelClass(),
                'template' => 'translation_model',
                'stub' => __DIR__ . '/../../resources/custom_translation/migration.stub'
            ]
        ];
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

    protected function arrayFields()
    {
        return FieldsService::parse($this->stringFields());
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

    protected function replacer()
    {
        return [
            '{belong_table_id}',
            Str::snake($this->baseNameModelClass()) . '_id'
        ];
    }
}
