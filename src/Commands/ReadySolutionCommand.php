<?php


namespace DenisKisel\LaravelAdminReadySolution\Commands;


use function GuzzleHttp\Psr7\str;
use Illuminate\Console\Command;

class ReadySolutionCommand extends Command
{
    protected $rootDir = __DIR__ . '/../../../../../';

    protected $patterns = [
        'page'
    ];
    /**
     * The name and signature of the console command.
     * larasol - laravel-admin solution ;)
     * {pattern} - page
     *
     * @var string
     */
    protected $signature = 'larasol {pattern} {--model=}';

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
        if (!$this->isPatternExists()) {
            $this->warn("Pattern: {$this->argument('pattern')} is not exists!");
            die();
        }

        $this->makeModel();
        $this->updateMigration();
        $this->createAdminController();
        $this->addRoute();
    }

    protected function model()
    {
        $model = $this->argument('pattern');
        if (!empty($this->option('model'))) {
            $model = $this->option('model');
        }
        return ucfirst($model);
    }

    protected function key()
    {
        $model =  $this->model();
        return strtolower($model);
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
        $files = array_merge(glob($this->rootDir . 'database/migrations/*.php'), glob("*.php"));
        $files = array_combine($files, array_map("filemtime", $files));
        arsort($files);
        $latestFile = key($files);

        $content = file_get_contents($latestFile);
        $content = str_replace('$table->timestamps();', file_get_contents(__DIR__ . "/../../resources/migration_stubs/{$this->argument('pattern')}.stub"), $content);

        file_put_contents($latestFile, $content);
        $this->info('Migration is updated!');
        $this->call('migrate');
    }

    protected function createAdminController()
    {
        $controllerStubPath = __DIR__ . "/../../resources/controller_stubs/{$this->argument('pattern')}.stub";
        $newControllerPath = $this->rootDir . "app/Admin/Controllers/{$this->model()}Controller.php";
        copy($controllerStubPath, $newControllerPath);

        $content = file_get_contents($newControllerPath);
        $content = str_replace('{model}', $this->model(), $content);
        $content = str_replace('{key}', $this->key(), $content);

        file_put_contents($newControllerPath, $content);
        $this->info('Admin controller is created!');
    }

    protected function addRoute()
    {
        $routesPath = $this->rootDir . '/app/Admin/routes.php';

        $newRoute = "\$router->resource('{$this->plural()}', '{$this->model()}Controller');";

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

    protected function plural()
    {
        if ($this->key()[-1] == 'y') {
            return substr($this->key(), 0, -1) . 'ies';
        }

        return $this->key() . 's';
    }

    protected function isPatternExists()
    {
        return in_array($this->argument('pattern'), $this->patterns);
    }
}
