<?php


namespace DenisKisel\Constructor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PageCommand extends Command
{
    /**
     * The name and signature of the console command.
     * larasol - laravel-admin solution ;)
     *
     * @var string
     */
    protected $signature = 'larasol:page {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laravel-admin ready solution';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->makeModel();
        $this->updateMigration();
        $this->createAdminController();
        $this->addRoute();
    }

    protected function model()
    {
        return Str::studly($this->argument('model'));
    }

    protected function key()
    {
        $model =  $this->model();
        return Str::snake($model);
    }

    protected function makeModel()
    {
        $this->call("make:model", [
            'name' => "App\\Models\\{$this->model()}",
            '-m' => '-m'
        ]);
    }

    protected function updateMigration()
    {
        $files = array_merge(glob(base_path('database/migrations/*.php')), glob("*.php"));
        $files = array_combine($files, array_map("filemtime", $files));
        arsort($files);
        $latestFile = key($files);

        $content = file_get_contents($latestFile);

        $migrationStub = __DIR__ . "/../../resources/page/migration.stub";
        $content = str_replace('$table->timestamps();', file_get_contents($migrationStub), $content);

        file_put_contents($latestFile, $content);
        $this->info('Migration is updated!');
        $this->call('migrate');
    }

    protected function createAdminController()
    {
        $controllerStubPath = __DIR__ . "/../../resources/page/controller.stub";
        $newControllerPath = app_path("Admin/Controllers/{$this->model()}Controller.php");
        copy($controllerStubPath, $newControllerPath);

        $content = file_get_contents($newControllerPath);
        $content = str_replace('{model}', $this->model(), $content);
        $content = str_replace('{key}', $this->key(), $content);

        file_put_contents($newControllerPath, $content);
        $this->info('Admin controller is created!');
    }

    protected function addRoute()
    {
        $routesPath = app_path('Admin/routes.php');

        $newRoute = "\$router->resource('{$this->slug()}', '{$this->model()}Controller');";

        $src = '], function (Router $router) {';
        $replace = <<<EOF
], function (Router \$router) {
    $newRoute
EOF;
        $content = file_get_contents($routesPath);

        if (strpos($content, "{$this->model()}Controller") !== false) {
            $this->info('Admin route already exists!');
        } else {
            $content = str_replace($src, $replace, $content);
            file_put_contents($routesPath, $content);
            $this->info('Admin route is created!');
        }
    }

    protected function slug()
    {
        return Str::slug(Str::plural($this->key()));
    }
}
