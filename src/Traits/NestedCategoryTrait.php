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
            $category = Category::find($categoryId);
            $this->pathByParent->push($category);
            if (!is_null($category->parent_id)) {
                $this->path($category->parent_id);
            }
        }

        return $this->pathByParent->reverse();
    }
}
