<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\AdminService;
use DenisKisel\Constructor\Services\FieldsService;
use Illuminate\Console\Command;

class InstallAdminLocaleCommand extends Command
{
    protected $signature = 'install:admin_locale';
    protected $description = 'Install admin locale';


    public function handle()
    {
        $this->call('construct:admin', [
            'model' => $this->modelClass(),
            '--controller_stub' =>  __DIR__ . '/../../resources/locale/admin_controller.stub'
        ]);
    }

    protected function modelClass()
    {
        return 'App\\Models\\Locale';
    }
}
