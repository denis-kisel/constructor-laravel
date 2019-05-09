<?php


namespace DenisKisel\Constructor\Commands;
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
        $this->call('construct:admin', [
            'model' => $this->argument('model'),
            '--fields' => $this->option('fields'),
            '--i' => $this->option('i')
        ]);
    }
}
