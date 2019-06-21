<?php


namespace DenisKisel\Constructor\Traits;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CategoryPathTrait
{
    public static function updatePaths()
    {
        $categoryClass = __CLASS__;
        $pathClass = __CLASS__ . 'Path';
        $pathClass::truncate();
        $categories = $categoryClass::all();

        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                $category->insertPath();
            }
        }
    }

    public function updatePath()
    {
        $pathClass = get_class($this) . 'Path';
        $category_field = Str::snake((new \ReflectionClass($this))->getShortName()) . '_id';
        $categories = $pathClass::select($category_field . ' as id')->wherePathId($this->id)->distinct()->get();
        $pathClass::whereIn($category_field, $categories->pluck('id', 'id')->toArray())->delete();
        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                $this->makeAndInsertPath($category->id);
            }
        }
    }

    public function insertPath()
    {
        $this->makeAndInsertPath($this->id);
    }

    public function makeAndInsertPath($id, $categories = [])
    {
        $categoryClass = get_class($this);
        $categoryShortClass = (new \ReflectionClass($this))->getShortName();
        $category = $categoryClass::find($id);
        $categories[] = $category->id;
        if ($category->hasParent()) {
            return $this->makeAndInsertPath($category->parent_id, $categories);
        } else {
            $categories = array_reverse($categories);
            $mainCategoryId = Arr::last($categories);

            $category_field = Str::snake($categoryShortClass) . '_id';

            $values = [];
            foreach ($categories as $key => $category) {
                $values[] = [
                    $category_field => $mainCategoryId,
                    'path_id' => $category,
                    'level' => $key,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $pathClass = get_class($this) . 'Path';
            $pathClass::insert($values);
        }
    }
}