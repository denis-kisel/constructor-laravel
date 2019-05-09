<?php


namespace DenisKisel\Constructor\Commands;

use Illuminate\Console\Command;

class InstallLocaleCommand extends Command
{
    protected $signature = 'install:locale {--m} {--i} {--a}';
    protected $description = 'Constructor for locale';

    public function handle()
    {
        $this->callModel();
        $this->callAdmin();
    }

    //STACK
    protected function callModel()
    {
        $this->call('construct:model', [
            'model' => $this->nameModelClass(),
            '--mig_stub' => __DIR__ . '/../../resources/locale/migration.stub',
            '--mig_replacer' => json_encode(false),
            '--m' => ($this->option('m')),
            '--i' => ($this->option('i')),
        ]);
    }

    protected function callAdmin()
    {
        if ($this->option('a')) {
            $this->call('install:admin_locale');
        }
    }

    //HELPER
    protected function nameModelClass()
    {
        return 'App\\Models\\Locale';
    }
}