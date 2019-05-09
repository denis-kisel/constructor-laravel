<?php


namespace DenisKisel\Constructor\Commands;

use DenisKisel\Constructor\Services\FieldsService;
use DenisKisel\Constructor\Services\MigrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PageCommand extends Command
{
    protected $signature = 'construct:page {model} {--fields=} {--a} {--i}';
    protected $description = 'Constructor of page';


    public function handle()
    {
        $this->callModel();
        $this->callAdmin();
    }

    protected function callModel()
    {
        $this->call('construct:model', [
            'model' => $this->argument('model'),
            '--fields' => $this->option('fields'),
            '--i' => $this->option('i'),
            '--mig_stub' => __DIR__ . '/../../resources/page/migration.stub'
        ]);
    }

    protected function callAdmin()
    {
        if ($this->option('a')) {
            $this->call('construct:admin_page', [
                'model' => $this->argument('model'),
                '--fields' => $this->option('fields'),
                '--i' => $this->option('i')
            ]);
        }
    }
}
