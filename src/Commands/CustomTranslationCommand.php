<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Exceptions\CustomTranslationCommandException;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CustomTranslationCommand extends Command
{
    const PHP_TAB = "\t";

    protected $migrationFieldsAvailable = [
        'bigIncrements',
        'bigInteger',
        'boolean',
        'char',
        'date',
        'dateTime',
        'dateTimeTz',
        'decimal',
        'double',
        'float',
        'geometry',
        'geometryCollection',
        'increments',
        'integer',
        'ipAddress',
        'json',
        'jsonb',
        'longText',
        'smallIncrements',
        'smallInteger',
        'string',
        'text',
        'time',
        'timeTz',
        'timestamp',
        'timestampTz',
        'tinyIncrements',
        'tinyInteger',
        'unsignedBigInteger',
        'unsignedDecimal',
        'unsignedInteger',
        'unsignedSmallInteger',
        'unsignedTinyInteger',
        'uuid',
        'year',
    ];


    protected $laravelAdminFields = [
        'bigIncrements' => 'number',
        'bigInteger' => 'number',
        'boolean' => 'switch',
        'char' => 'text',
        'date' => 'date',
        'dateTime' => 'datetime',
        'dateTimeTz' => 'datetime',
        'decimal' => 'currency',
        'double' => 'currency',
        'float' => 'currency',
        'geometry' => 'text',
        'geometryCollection' => 'text',
        'increments' => 'number',
        'integer' => 'number',
        'ipAddress' => 'ip',
        'json' => 'text',
        'jsonb' => 'text',
        'longText' => 'summernote',
        'smallIncrements' => 'number',
        'smallInteger' => 'number',
        'string' => 'text',
        'text' => 'summernote',
        'time' => 'time',
        'timeTz' => 'time',
        'timestamp' => 'text',
        'timestampTz' => 'text',
        'tinyIncrements' => 'number',
        'tinyInteger' => 'number',
        'unsignedBigInteger' => 'number',
        'unsignedDecimal' => 'currency',
        'unsignedInteger' => 'number',
        'unsignedSmallInteger' => 'number',
        'unsignedTinyInteger' => 'number',
        'uuid' => 'text',
        'year' => 'text',
    ];

    /**
     * The name and signature of the console command.
     * fields: field_name:{type}:{length}[nullable]
     * {type} - string|integer|text
     *
     * ct - custom translation
     *
     * @var string
     */
    protected $signature = 'construct:ct {model} {fields} {--o}';

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

    protected function makeModel()
    {
        if ($this->option('o')) {
            return;
        }
        $this->call("make:model", [
            'name' => $this->argument('model'),
        ]);
    }

    protected function shortModelClassName()
    {
        $reflection = new \ReflectionClass($this->argument('model'));
        return $reflection->getShortName();
    }

    protected function fields()
    {
        $fields = [];
        foreach (explode(',', $this->argument('fields')) as $item) {
            $isNullable = false;
            if (Str::contains($item, '[nullable]')) {
                $isNullable = true;
                $item = str_replace('[nullable]', '', $item);
            }

            $parts = explode(':', $item);

            if (count($parts) < 2) {
                throw new CustomTranslationCommandException('Wrong field: ' . $item . '. Right pattern: name:type:length[nullable]');
            }

            $fields[] = [
                'name' => $parts[0],
                'type' => $parts[1],
                'length' => (!empty($parts[2])) ? $parts[2] : null,
                'is_nullable' => $isNullable
            ];
        }

        return $fields;
    }

    protected function makeMigration()
    {
        $table = Str::plural(Str::snake($this->shortModelClassName()));
        $migrationPath = database_path('migrations/' . date('Y_m_d_His') . "_create_{$table}_table.php");
        $stub = __DIR__ . '/../../resources/custom_translation/migration.stub';

        copy($stub, $migrationPath);

        $content = file_get_contents($migrationPath);
        $class = Str::studly($table);
        $content = str_replace('{class}', "Create{$class}Table", $content);
        $content = str_replace('{table}', $table, $content);
        $content = str_replace('{fields}', $this->makeMigrationFields(), $content);
        file_put_contents($migrationPath, $content);
        $this->info('Migration is created!');
//        $this->call('migrate');
    }

    protected function makeMigrationFields()
    {
        $output = '$table->increments(\'id\');' . PHP_EOL;
        foreach ($this->fields() as $field) {
            if (!is_null($field['length']) && !$field['is_nullable']) {
                $output .= $this->formatText("\$table->{$field['type']}('{$field['name']}', {$field['length']});");
            } else if (!is_null($field['length']) && $field['is_nullable']) {
                $output .= $this->formatText("\$table->{$field['type']}('{$field['name']}', {$field['length']})->nullable();");
            } else if (is_null($field['length']) && $field['is_nullable']) {
                $output .= $this->formatText("\$table->{$field['type']}('{$field['name']}')->nullable();");
            } else {
                $output .= $this->formatText("\$table->{$field['type']}('{$field['name']}');");
            }
        }
        $output .= $this->formatText('$table->timestamps();');

        return $output;
    }

    protected function makeAdminFields()
    {
        $output = '';
        foreach ($this->fields() as $field) {
            if ($field['is_nullable']) {
                $output .= $this->formatText("\$form->{$this->laravelAdminFields[$field['type']]}('{$field['name']}', __('admin.{$field['name']}'));", 2);
            } else {
                $output .= $this->formatText("\$form->{$this->laravelAdminFields[$field['type']]}('{$field['name']}', __('admin.{$field['name']}'))->required();", 2);
            }
        }

        return $output;
    }

    protected function makeAdminGrids()
    {
        $output = '';
        foreach ($this->fields() as $field) {
            if ($field['name'] == 'image') {
                $output .= <<<EOF
        \$grid->{$field['name']}( __('admin.{$field['name']}'))->display(function(\$image) {
            \$src = (\$image) ? SmartImage::cache(\$image, 100, 100) : '';
            return (\$src) ? "<img src='{\$src}'>" : '';
        });
                
EOF;
            } else {
                $output .= $this->formatText("\$grid->{$field['name']}( __('admin.{$field['name']}'))->editable('text');", 2);
            }
        }
        return $output;
    }

    protected function createAdminControllers()
    {

        $controllerStubPath = __DIR__ . "/../../resources/custom_translation/controller.stub";
        $newControllerPath = app_path("Admin/Controllers/{$this->shortModelClassName()}Controller.php");
        copy($controllerStubPath, $newControllerPath);

        $title = Str::snake($this->shortModelClassName());
        $title = str_replace('_', ' ', $title);
        $title = Str::title($title);

        $content = file_get_contents($newControllerPath);
        $content = str_replace('{modelShortName}', $this->shortModelClassName(), $content);
        $content = str_replace('{controllerClass}', "{$this->shortModelClassName()}Controller", $content);
        $content = str_replace('{modelClass}', "{$this->argument('model')}", $content);
        $content = str_replace('{title}', $title, $content);
        $content = str_replace('{form}', $this->makeAdminFields(), $content);
        $content = str_replace('{grid}', $this->makeAdminGrids(), $content);

        file_put_contents($newControllerPath, $content);
        $this->info("Admin {$this->shortModelClassName()} controller is created!");
    }

    protected function addRoute()
    {
        $routesPath = app_path('Admin/routes.php');

        $slug = Str::slug(Str::plural(Str::snake($this->shortModelClassName())), '-');
        $newRoute = "\$router->resource('{$slug}', '{$this->shortModelClassName()}Controller');";

        $src = '], function (Router $router) {';
        $replace = <<<EOF
], function (Router \$router) {
    $newRoute
EOF;
        $content = file_get_contents($routesPath);

        if (Str::contains($content, "{$this->shortModelClassName()}Controller")) {
            $this->info("Admin route for {$this->shortModelClassName()} model already exists!");
        } else {
            $content = str_replace($src, $replace, $content);
            file_put_contents($routesPath, $content);
            $this->info("Admin route for {$this->shortModelClassName()} model is created!");
        }
    }

    protected function stopIfModelExists()
    {
        if ($this->option('o')) {
            return;
        }
        if (class_exists($this->argument('model'))) {
            $this->warn('This model is already exists!');
            die();
        }
    }

    protected function formatText($text, $countTabs = 3)
    {
        return str_repeat(self::PHP_TAB, $countTabs) . $text . PHP_EOL;
    }
}
