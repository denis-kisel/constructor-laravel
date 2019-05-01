<?php


namespace DenisKisel\Constructor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CategoryProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     * larasol - laravel-admin solution ;)
     *
     * @var string
     */
    protected $signature = 'larasol:category-product {category_model} {product_model}';

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
        $this->stopIfModelExists();
        $this->makeModel();
        $this->makeMigration();
        $this->createAdminControllers();
        $this->addRoute();
    }

    protected function categoryModel()
    {
        return Str::studly($this->argument('category_model'));
    }

    protected function productModel()
    {
        return Str::studly($this->argument('product_model'));
    }

    protected function categoryProductModel()
    {
        return $this->categoryModel() . $this->productModel();
    }

    protected function productImageModel()
    {
        return $this->productModel() . 'Image';
    }

    protected function makeModel()
    {
        $this->call("make:model", [
            'name' => "App\\Models\\{$this->categoryModel()}",
        ]);

        $this->call("make:model", [
            'name' => "App\\Models\\{$this->categoryProductModel()}",
        ]);

        $models = [
            'product' => $this->productModel(),
            'product_image' => $this->productImageModel()
        ];

        foreach ($models as $stub => $model) {
            $productStub = __DIR__ . "/../../resources/category_product/model_{$stub}.stub";
            $productPath = app_path('Models/' . $model . '.php');
            copy($productStub, $productPath);

            $content = file_get_contents($productPath);
            $content = str_replace('{product}', $this->productModel(), $content);
            $content = str_replace('{category}', $this->categoryModel(), $content);
            $content = str_replace('{product_image}', $this->productImageModel(), $content);

            file_put_contents($productPath, $content);

            $this->info("Model {$model} is created!");
        }
    }

    protected function makeMigration()
    {

        $table = Str::snake($this->categoryProductModel());
        $migrationPath = database_path('migrations/' . date('Y_m_d_His') . "_create_{$table}_table.php");
        $stub = __DIR__ . '/../../resources/category_product/migration.stub';

        copy($stub, $migrationPath);

        $content = file_get_contents($migrationPath);
        $content = str_replace('{class}', "Create{$this->categoryProductModel()}Table", $content);
        $content = str_replace('{categories_table}', Str::plural(Str::snake($this->categoryModel())), $content);
        $content = str_replace('{products_table}', Str::plural(Str::snake($this->productModel())), $content);
        $content = str_replace('{product_images_table}', Str::plural(Str::snake($this->productImageModel())), $content);
        $content = str_replace('{category_product_table}', Str::snake($this->categoryProductModel()), $content);
        $content = str_replace('{product_id}', Str::snake($this->productModel()) . '_id', $content);
        $content = str_replace('{category_id}', Str::snake($this->categoryModel()) . '_id', $content);

        file_put_contents($migrationPath, $content);
        $this->info('Migration is created!');
        $this->call('migrate');
    }

    protected function createAdminControllers()
    {
        $controllers = [
            'category' => $this->categoryModel(),
            'product' => $this->productModel()
        ];

        foreach ($controllers as $stub => $model) {
            $controllerStubPath = __DIR__ . "/../../resources/category_product/controller_{$stub}.stub";
            $newControllerPath = app_path("Admin/Controllers/{$model}Controller.php");
            copy($controllerStubPath, $newControllerPath);

            $content = file_get_contents($newControllerPath);
            $content = str_replace('{model}', $model, $content);
            $content = str_replace('{category_model}', $this->categoryModel(), $content);
            $content = str_replace('{title}', Str::title($model), $content);

            file_put_contents($newControllerPath, $content);
            $this->info("Admin {$model} controller is created!");
        }
    }

    protected function addRoute()
    {
        $routesPath = app_path('Admin/routes.php');

        $models = [
            $this->productModel(),
            $this->categoryModel()
        ];

        foreach ($models as $model) {
            $newRoute = "\$router->resource('{$this->slug($model)}', '{$model}Controller');";

            $src = '], function (Router $router) {';
            $replace = <<<EOF
], function (Router \$router) {
    $newRoute
EOF;
            $content = file_get_contents($routesPath);

            if (strpos($content, "{$model}Controller") !== false) {
                $this->info("Admin route for {$model} model already exists!");
            } else {
                $content = str_replace($src, $replace, $content);
                file_put_contents($routesPath, $content);
                $this->info("Admin route for {$model} model is created!");
            }
        }
    }

    protected function slug($text)
    {
        return Str::kebab(Str::plural($text));
    }

    protected function stopIfModelExists()
    {
        if (file_exists(app_path("Models/{$this->productModel()}.php")) || file_exists(app_path("Models/{$this->categoryModel()}.php"))) {
            $this->warn('One or all models is/are already exists!');
            die();
        }
    }
}
