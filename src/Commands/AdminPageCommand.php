<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\AdminService;
use DenisKisel\Constructor\Services\FieldsService;
use Illuminate\Console\Command;

class AdminPageCommand extends Command
{
    protected $signature = 'construct:admin_page {model} {--fields=} {--i}';
    protected $description = 'Construct admin page from model';


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
        return FieldsService::parse($this->option('fields'));
    }

    protected function baseNameModelClass()
    {
        return class_basename($this->argument('model'));
    }

    protected function makeController()
    {
        AdminService::makeController(
            $this->argument('model'),
            AdminService::generateForm($this->fields()),
            AdminService::generateGrid($this->fields()),
            null,
            __DIR__ . '/../../resources/page/admin_controller.stub'
        );

        $this->info("Admin {$this->baseNameModelClass()}Controller is created!");
    }

    protected function addRoute()
    {
        $this->info(AdminService::addRoute($this->baseNameModelClass()));
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
    }
}
