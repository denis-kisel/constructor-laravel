<?php


namespace DenisKisel\Constructor\Traits;

use Illuminate\Support\Str;

trait CommonPageTrait
{
    use HrefTrait;


    public function titleOrName()
    {
        return (!empty($this->title)) ? $this->title : $this->name;
    }

    public function h1OrName()
    {
        return (!empty($this->h1)) ? $this->h1 : $this->name;
    }

    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    public function canonical()
    {
        return $this->href();
    }

    public function isAllowedIndex()
    {
        return $this->allow_index ?? 0;
    }
}
