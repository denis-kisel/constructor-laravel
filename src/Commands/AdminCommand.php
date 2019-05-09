<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\AdminService;
use DenisKisel\Constructor\Services\FieldsService;
use Illuminate\Console\Command;

class AdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     * fields: field_name:data_type:length{migration_methods}
     * {type} - string|integer|text
     *
     * Example: construct:admin App\\Models\\Page name:string,description:text{nullable},is_active:boolean{nullable+default:1}
     *
     * @var string
     */
    protected $signature = 'construct:admin {model} {--fields=} {--i} {--controller_stub=} {--controller_replacer=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Construct admin from model';
    protected $controllerStubPath = __DIR__ . '/../../resources/custom/admin_controller.stub';


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

    //STACK
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

    protected function makeController()
    {
        AdminService::makeController(
            $this->argument('model'),
            $this->collectionFields()->toArray(),
            $this->stubPath(),
            $this->replacer()
        );

        $this->info("Admin {$this->baseNameModelClass()}Controller is created!");
    }

    protected function addRoute()
    {
        $this->info(AdminService::addRoute($this->baseNameModelClass()));
    }


    //HELPERS
    public function stringFields()
    {
        return $this->option('fields');
    }

    public function collectionFields()
    {
        return FieldsService::parse($this->stringFields());
    }

    public function stubPath()
    {
        $output = $this->controllerStubPath;
        if ($this->option('controller_stub')) {
            $output = $this->option('controller_stub');
        }

        return $output;
    }

    public function replacer() {
        $output = null;
        if ($this->option('controller_replacer')) {
            $output = json_decode($this->option('controller_replacer'));
        }

        return $output;
    }

    protected function baseNameModelClass()
    {
        return class_basename($this->argument('model'));
    }
}
