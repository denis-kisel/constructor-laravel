<?php


namespace DenisKisel\Constructor\Commands;


use App\Helpers\ExecTime;
use App\Models\Category;
use Illuminate\Console\Command;

class UpdateCategoryPathCommand extends Command
{
    protected $signature = 'category:update_path {--category=}';
    protected $description = 'Update Category Path';


    public function handle()
    {
        if ($this->option('category')) {
            $this->updateOne();
        } else {
            $this->updateAll();
        }
    }

    protected function updateOne()
    {
        $category = Category::find($this->option('category'));
        $category->updateOwnPath();
        $this->info("Category Path For {$category->name} Is Updated!");
    }

    protected function updateAll()
    {
        $time = new ExecTime();
        $cats = Category::all();
        if ($cats->count() > 0) {
            foreach ($cats as $cat) {
                $cat->updateOwnPath();
                $this->info("Updated: {$cat->name} ($cat->id)");
            }
        }

        $this->info("Time ~{$time->showTime()} seconds");
    }
}
