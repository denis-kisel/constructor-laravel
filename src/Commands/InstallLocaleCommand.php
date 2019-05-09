<?php


namespace DenisKisel\Constructor\Commands;

class InstallLocaleCommand extends ModelCommand
{
    protected $signature = 'install:locale {--m} {--i} {--a}';
    protected $description = 'Constructor for locale';
    protected $migrationStub = __DIR__ . '/../../resources/locale/migration.stub';

    protected function makeAdminController()
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

    protected function fields()
    {
        return [];
    }

    protected function replacer()
    {
        return  [];
    }
}