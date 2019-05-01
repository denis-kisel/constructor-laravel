<?php


namespace DenisKisel\Constructor\Traits;


trait HrefTrait
{
    public function href()
    {
        return url($this->prefixSlug . $this->slug);
    }
}
