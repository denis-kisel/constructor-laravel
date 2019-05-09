<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\FieldsService;
use DenisKisel\Constructor\Services\MigrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'construct:page {model} {--fields=} {--a}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Constructor of page';

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
        $this->makeAdminController();
    }

    protected function stopIfModelExists()
    {
        if (class_exists($this->className())) {
            $this->warn("{$this->className()} model is already exists!");
            die();
        }
    }

    protected function className()
    {
        return $this->argument('model');
    }

    protected function basenameClassName()
    {
        return class_basename($this->className());
    }

    protected function makeModel()
    {
        $this->call('make:model', [
            'name' => $this->className()
        ]);
    }

    protected function makeMigration()
    {
        MigrationService::create(
            $this->basenameClassName(),
            __DIR__ . '/../../resources/page/migration.stub',
            [
                [
                    '{class_name}',
                    '{table}',
                    '{fields}'
                ],
                [
                    Str::plural($this->basenameClassName()),
                    Str::plural(Str::snake($this->basenameClassName())),
                    MigrationService::generateMigrationFields(
                        $this->collectionFields()->toArray(), false, null, false, false
                    )
                ]
            ]
        );

//        $this->call('migrate');
    }

    protected function makeAdminController()
    {
        if ($this->option('a')) {
            $this->call('construct:admin_page', [
                'model' => $this->className(),
                '--fields' => $this->option('fields')
            ]);
        }
    }

    protected function collectionFields()
    {
        return FieldsService::parse($this->option('fields'));
    }
}
