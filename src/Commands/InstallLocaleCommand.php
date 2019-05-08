<?php


namespace DenisKisel\Constructor\Commands;


use DenisKisel\Constructor\Services\MigrationService;
use Illuminate\Console\Command;

class InstallLocaleCommand extends Command
{
    protected $signature = 'install:locale {--a}';
    protected $description = 'Constructor for locale';


    public function handle()
    {
        $this->stopIfModelExists();
        $this->makeModel();
        $this->makeMigration();
        $this->makeAdminController();
    }

    protected function stopIfModelExists()
    {
        if (class_exists($this->className())) {
            $this->warn('Locale model is already exists!');
            die();
        }
    }

    protected function className()
    {
        return 'App\\Models\\Locale';
    }

    protected function basenameClassName()
    {
        return class_basename($this->className());
    }

    protected function makeModel()
    {
        $this->call('make:model', [
            'name' => $this->className()
        ]);
    }

    protected function makeMigration()
    {
        MigrationService::create(
            $this->basenameClassName(),
            __DIR__ . '/../../resources/locale/migration.stub'
        );

        $this->call('migrate');
    }

    protected function makeAdminController()
    {
        if ($this->option('a')) {
            $this->call('install:admin_locale');
        }
    }
}