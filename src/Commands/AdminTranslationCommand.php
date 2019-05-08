<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\AdminService;
use DenisKisel\Constructor\Services\FieldsService;
use Illuminate\Console\Command;

class AdminTranslationCommand extends Command
{
    /**
     * The name and signature of the console command.
     * fields: field_name:data_type:length{migration_methods}
     * {type} - string|integer|text
     *
     * Example: construct:admint App\\Models\\Page name:string[t],description:text{nullable}[t],image:string{nullable},is_active:boolean{nullable+default:1}
     *
     * @var string
     */
    protected $signature = 'construct:admint {model} {fields} {--i}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Construct admin translation from model';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->stopIfControllerExists();
        $this->makeController();
        $this->addRoute();
    }

    public function fields()
    {
        return FieldsService::parse($this->argument('fields'));
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

    protected function models()
    {
        return [
            [
                'class' => $this->argument('model'),
                'class_basename' => $this->baseNameModelClass(),
                'template' => 'controller'
            ],
            [
                'class' => $this->translationModelClass(),
                'class_basename' => $this->translationBaseModelClass(),
                'template' => 'controller_translation'
            ]
        ];
    }

    protected function makeController()
    {
        AdminService::makeControllerTranslation(collect($this->models()), $this->fields());
        $this->info("Admin {$this->baseNameModelClass()}Controller is created!");
    }

    protected function addRoute()
    {
        foreach ($this->models() as $model) {
            $this->info(AdminService::addRoute(class_basename($model['class'])));
        }
    }

    protected function stopIfControllerExists()
    {
        if ($this->option('i')) {
            return;
        }

        if (class_exists('App\\Admin\\Controllers\\' . $this->baseNameModelClass() . 'Controller')) {
            $this->warn("This admin {$this->baseNameModelClass()}Controller is already exists!");
            die();
        }

        if (class_exists('App\\Admin\\Controllers\\' . $this->baseNameModelClass() . 'TranslationController')) {
            $this->warn("This admin {$this->baseNameModelClass()}TranslationController is already exists!");
            die();
        }
    }
}
