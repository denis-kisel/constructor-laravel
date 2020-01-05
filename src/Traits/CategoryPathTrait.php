<?php


namespace DenisKisel\Constructor\Traits;


use App\Models\CategoryPath;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CategoryPathTrait
{
    public static function updateAllPath()
    {
        $categoryClass = __CLASS__;
        $pathClass = __CLASS__ . 'Path';
        $pathClass::truncate();
        $categories = $categoryClass::all();

        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                $category->updateOwnPath();
            }
        }
    }

    public function updateOwnPath()
    {
        $paths = $this->path($this->id);
        $pathIds = $paths->pluck('id')->toArray();
        CategoryPath::whereCategoryId($this->id)->delete();
        if ($pathIds) {
            $inserts = [];
            foreach ($pathIds as $level => $pathId) {
                $inserts[] = [
                    'category_id' => $this->id,
                    'path_id' => $pathId,
                    'level' => $level
                ];
            }
            CategoryPath::insertIgnore($inserts);
        }
    }

    /** @return Array */
    public function nestedCategoryIds()
    {
        return CategoryPath::wherePathId($this->id)->pluck('category_id')->toArray();
    }
}
