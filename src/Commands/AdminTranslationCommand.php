<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\AdminService;
use DenisKisel\Constructor\Services\FieldsService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminTranslationCommand extends Command
{
    /**
     * Example: construct:admint App\\Models\\Page --fields=name:string[t],description:text{nullable}[t],image:string{nullable},is_active:boolean{nullable+default:1}
     */
    protected $signature = 'construct:admint {model} {--fields=} {--i}';
    protected $description = 'Construct admin translation from model';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->models() as $model) {
            $this->call('construct:admin', [
                'model' => $model['class'],
                '--fields' => $this->option('fields'),
                '--i' => $this->option('i'),
                '--controller_stub' => $model['controller_stub'],
                '--controller_replacer' => $this->controllerReplacer()
            ]);
        }
    }

    protected function models()
    {
        return [
            [
                'class' => $this->argument('model'),
                'controller_stub' => $this->controllerStub()['c'],
            ],
            [
                'class' => $this->translationModelClass(),
                'controller_stub' => $this->controllerStub()['ct'],
            ]
        ];
    }

    protected function controllerStub()
    {
        if ($this->option('controller_stub')) {
            return json_decode($this->option('controller_stub'));
        }
        return [
            'c' => __DIR__ . '/../../resources/custom_translation/controller.stub',
            'ct' => __DIR__ . '/../../resources/custom_translation/controller_translation.stub',
        ];
    }

    protected function controllerReplacer()
    {
        if ($this->option('controller_replacer')) {
            return $this->option('controller_replacer');
        }
        return json_encode([
            ['{baseForm}', '{translationForm}',  '{belongModelClass}','{belongBasenameModelClass}', '{belongModelId}', '{belongModelLangKey}'],
            [
                AdminService::generateForm($this->collectionFields()->where('is_translation', '=', false)->toArray()),
                AdminService::generateForm($this->collectionFields()->where('is_translation', '=', true)->toArray(), 4),
                $this->argument('model'),
                $this->basenameModelClass(),
                Str::snake($this->basenameModelClass()) . '_id',
                Str::snake($this->basenameModelClass())
            ]
        ]);
    }

    public function collectionFields()
    {
        return FieldsService::parse($this->option('fields'));
    }

    protected function basenameModelClass()
    {
        return class_basename($this->argument('model'));
    }

    protected function translationModelClass()
    {
        return $this->argument('model') . 'Translation';
    }
}
