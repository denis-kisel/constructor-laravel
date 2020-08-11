<?php

namespace DenisKisel\Constructor\Traits;


use App\Models\Category;
use Tightenco\Collect\Support\Collection;

trait NestedCategoryTrait
{
    /** @var Collection */
    protected $pathByParent = null;

    public function getNestedNameAttribute()
    {
        return $this->combine($this->id);
    }

    public function getPathAttribute()
    {
        return $this->path($this->id);
    }

    public function combine($id, $categories = [])
    {
        $category = get_class($this)::find($id);
        if ($category->hasParent()) {
            $categories[] = $category->name;
            return $this->combine($category->parent_id, $categories);
        } else {
            $categories[] = $category->name;
            $categories = array_reverse($categories);
            return implode(' -> ', $categories);
        }
    }

    public function hasParent()
    {
        return (bool)$this->parent_id;
    }

    public function path($categoryId)
    {
        if (is_null($this->pathByParent)) {
            $this->pathByParent = collect([]);
        }
        if (!is_null($categoryId)) {
            $category = Category::with('translations')->whereId($categoryId)->whereTranslation('locale', config('app.locale'))->first();
            $this->pathByParent->push($category);
            if (!empty($category->parent_id)) {
                $this->path($category->parent_id);
            }
        }

        return $this->pathByParent->reverse();
    }

    public static function arrayTree($parentId = null)
    {
        $categories = Category::with('translations');

        if (is_null($parentId)) {
            $categories->whereNull('parent_id');
        } else {
            $categories->whereParentId($parentId);
        }

        $categoryItems = $categories->whereTranslation('locale', config('app.locale'))->orderBy('sort')->get();
        $tree = [];
        if ($categoryItems->isNotEmpty()) {
            foreach ($categoryItems as $category) {
                if ($category->is_active) {
                    $tree[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'href' => $category->href(),
                        'children' => self::arrayTree($category->id)
                    ];
                }
            }
        }

        return $tree;
    }
}
