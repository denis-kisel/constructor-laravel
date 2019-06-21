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
        file_exists(config_path('image.php')) || copy(__DIR__ . '/../../config/image.php', config_path('image.php'));

        File::makeDirectory(storage_path('app/public'), 0775, true, true);
        file_exists(storage_path('app/public/placeholder.png')) || copy(__DIR__ . '/../../resources/image/placeholder.png', storage_path('app/public/placeholder.png'));
        chmod(storage_path('app/public/placeholder.png'), 0775);
    }
}