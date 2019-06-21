<?php


namespace DenisKisel\Constructor\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallImageCommand extends Command
{
    protected $signature = 'install:construct_image';
    protected $description = 'Public image config and placeholder';

    public function handle()
    {
        copy(__DIR__ . '/../../config/image.php', config_path('image.php'));

        File::makeDirectory(storage_path('app/public'), '775', true, true);
        copy(__DIR__ . '/../../resources/image/placeholder.png', storage_path('app/public/image.php'));
    }
}