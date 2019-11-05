<?php


namespace DenisKisel\Constructor\Traits;

use DenisKisel\SmartImage\SmartImage;

trait ImageSrcTrait
{
    public function imageSrc($with = null, $height = null, $imageField = 'image')
    {
        $image = (empty($this->{$imageField}) || !file_exists(storage_path('app/public/' . $this->{$imageField}))) ? config('image.placeholder') : $this->{$imageField};

        return SmartImage::cache($image, $with, $height);
    }
}
