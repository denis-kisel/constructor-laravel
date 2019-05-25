<?php


namespace DenisKisel\Constructor\Commands;


use Illuminate\Console\Command;

class PaymentCommand extends Command
{
    protected $signature = 'construct:payment {--model=} {--a} {--i} {--m}';
    protected $description = 'Constructor of payment';


    public function handle()
    {
        $this->callModel();
        $this->callAdmin();
    }

    protected function callModel()
    {
        $this->call('construct:model', [
            'model' => $this->model(),
            '--fields' => $this->fields(),
            '--i' => $this->option('i'),
            '--m' => $this->option('m')
        ]);
    }

    protected function callAdmin()
    {
        if ($this->option('a')) {
            $this->call('construct:admin_page', [
                'model' => $this->model(),
                '--fields' => $this->fields(),
                '--i' => $this->option('i')
            ]);
        }
    }

    //HELPER
    protected function model()
    {
        if ($this->option('model')) {
            return $this->option('model');
        }

        return 'App\\Models\\Payment';
    }

    protected function fields()
    {
        return include(__DIR__ . '/../../resources/patterns/payment.php');
    }
}
