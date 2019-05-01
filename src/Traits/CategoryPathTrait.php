<?php


namespace DenisKisel\Constructor\Traits;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CategoryPathTrait
{
    public function updatePaths()
    {
        $categories = get_class($this)::all();

        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                $this->makeAndInsertPath($category->id);
            }
        }
    }

    public function updatePath()
    {
        $ids = $this->deletePath();
        if ($ids) {
            foreach ($ids as $id) {
                $this->makeAndInsertPath($id);
            }
        }
    }

    public function insertPath()
    {
        $this->makeAndInsertPath($this->id);
    }

    public function deletePath()
    {
        $pathClass = get_class($this) . 'Path';
        $category_field = Str::snake(get_class($this)) . '_id';
        $categories = $pathClass::select($category_field . ' as id')->wherePathId($this->id)->distinct()->get();
        $pathClass::whereIn($category_field, $categories->pluck('id', 'id')->toArray())->delete();
        return $categories->pluck('id', 'id')->toArray();
    }

    public function makeAndInsertPath($id, $categories = [])
    {
        $category = get_class($this)::find($id);
        $categories[] = $category->id;
        if ($category->hasParent()) {
            return $this->makeAndInsertPath($category->parent_id, $categories);
        } else {
            $categories = array_reverse($categories);
            $mainCategoryId = Arr::last($categories);

            $category_field = Str::snake(get_class($this)) . '_id';

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