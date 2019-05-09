<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\FieldsService;
use DenisKisel\Constructor\Services\MigrationService;
use DenisKisel\Constructor\Services\ModelService;
use Illuminate\Console\Command;

class ModelCommand extends Command
{
    protected $migrationStubPath = __DIR__ . '/../../resources/custom/migration.stub';
    protected $modelStubPath = __DIR__ . '/../../resources/custom/model.stub';

    /**
     * The name and signature of the console command.
     * fields: field_name:data_type:length{migration_methods}
     * {type} - string|integer|text
     *
     * Example: construct:model App\\Models\\Page --fields=name:string,description:text{nullable},is_active:boolean{nullable+default:1}
     *
     * @var string
     */
    protected $signature = 'construct:model {model} {--fields=} {--i} {--m} {--a} {--mig_stub=} {--mig_replacer=} {--model_stub} {--model_replacer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Constructor for models';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->stopIfModelExists();
        $this->makeModel();
        $this->makeMigration();
        $this->makeAdminController();
    }

    //STACK
    protected function stopIfModelExists()
    {
        if ($this->option('i')) {
            return;
        }
        if (class_exists($this->nameModelClass())) {
            $this->warn('This model is already exists!');
            die();
        }
    }

    protected function makeModel()
    {
        ModelService::create($this->nameModelClass(), $this->modelStubPath(), $this->modelReplacer());
        $this->info("Model {$this->nameModelClass()} is created!");

        if ($this->option('m')) {
            $this->call('migrate');
        }
    }

    protected function makeMigration()
    {
        MigrationService::create(
            $this->baseNameModelClass(),
            MigrationService::generateMigrationFields($this->collectionFields()->toArray()),
            $this->migrationStubPath(),
            $this->migrationReplacer()
        );

        $this->info('Migration is created!');

        if ($this->option('m')) {
            $this->call('migrate');
        }
    }

    protected function makeAdminController()
    {
        if ($this->option('a')) {
            $this->call('construct:admin', [
                'model' => $this->nameModelClass(),
                'fields' => $this->stringFields(),
                '--i' => ($this->option('i')) ? '--i' : null,
            ]);
        }
    }

    //HELPERS
    protected function nameModelClass()
    {
        return $this->argument('model');
    }

    protected function baseNameModelClass()
    {
        return class_basename($this->nameModelClass());
    }

    protected function stringFields()
    {
        return $this->option('fields');
    }

    protected function collectionFields()
    {
        return FieldsService::parse($this->stringFields());
    }

    protected function migrationStubPath()
    {
        $output = $this->migrationStubPath;
        if ($this->option('mig_stub')) {
            $output = $this->option('mig_stub');
        }

        return $output;
    }

    protected function migrationReplacer()
    {
        if ($this->option('mig_replacer')) {
            return json_decode($this->option('mig_replacer'));
        }
    }

    protected function modelStubPath()
    {
        $output = $this->modelStubPath;
        if ($this->option('model_stub')) {
            $output = $this->option('model_stub');
        }

        return $output;
    }

    protected function modelReplacer()
    {
        if ($this->option('model_replacer')) {
            return json_decode($this->option('model_replacer'));
        }
    }
}
