<?php

namespace DenisKisel\Constructor\Traits;


trait NestedCategoryTrait
{
    public function getNestedAttribute()
    {
        return $this->combine($this->id);
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
}