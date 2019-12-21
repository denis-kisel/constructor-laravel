<?php


namespace DenisKisel\Constructor\Traits;


use Illuminate\Support\Str;

trait HrefTrait
{
    public function href()
    {
        return url(Str::finish($this->prefixSlug, '/') . $this->slug);
    }
}
