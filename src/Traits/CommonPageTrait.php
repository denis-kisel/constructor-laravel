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
}
