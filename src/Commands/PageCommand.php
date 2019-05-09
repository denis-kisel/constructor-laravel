<?php


namespace DenisKisel\Constructor\Commands;


use Illuminate\Console\Command;

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
            '--fields' => $this->fields(),
            '--i' => $this->option('i'),
        ]);
    }

    protected function callAdmin()
    {
        if ($this->option('a')) {
            $this->call('construct:admin_page', [
                'model' => $this->argument('model'),
                '--fields' => $this->fields(),
                '--i' => $this->option('i')
            ]);
        }
    }

    //HELPER
    protected function fields()
    {
        $fields = include(__DIR__ . '/../../resources/patterns/page.php');
        if ($this->option('fields')) {
            $fields = str_replace('{option_fields}', $this->option('fields'), $fields);
        }

        $fields = str_replace(',{option_fields}', '', $fields);
        return $fields;
    }
}
