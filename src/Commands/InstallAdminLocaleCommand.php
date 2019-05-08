<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\AdminService;
use DenisKisel\Constructor\Services\FieldsService;
use Illuminate\Console\Command;

class InstallAdminLocaleCommand extends Command
{
    protected $signature = 'install:admin_locale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install admin locale';


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

    protected function modelClass()
    {
        return 'App\\Models\\Locale';
    }

    protected function baseNameModelClass()
    {
        return class_basename($this->modelClass());
    }

    protected function makeController()
    {
        AdminService::makeController(
            $this->modelClass(),
            null,
            null,
            null,
            __DIR__ . '/../../resources/locale/admin_controller.stub'
        );

        $this->info("Admin {$this->baseNameModelClass()}Controller is created!");
    }

    protected function addRoute()
    {
        $this->info(AdminService::addRoute($this->baseNameModelClass()));
    }

    protected function stopIfControllerExists()
    {
        if (class_exists('App\\Admin\\Controllers\\' . $this->baseNameModelClass() . 'Controller')) {
            $this->warn("This admin {$this->baseNameModelClass()}Controller is already exists!");
            die();
        }
    }
}
