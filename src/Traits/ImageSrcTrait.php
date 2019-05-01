<?php


namespace DenisKisel\Constructor\Traits;

use DenisKisel\Constructor\Exceptions\ImageException;
use DenisKisel\SmartImage\SmartImage;

trait ImageSrcTrait
{
    public function imageSrc($size = null)
    {
        $sizes = [null, null];
        if (!is_null($size)) {
            if (is_string($size)) {
                $sizes = config("image.sizes.{$size}");
            } elseif (is_array($size) && count($size) == 2) {
                $sizes = array_values($size);
            } elseif (is_array($size) && count($size) != 2) {
                throw new ImageException('Wrong parameter size! Array must be 2 elements! File: ' . __FILE__ . ' Line: ' . __LINE__);
            }
        }

        return SmartImage::cache($this->image, $sizes[0], $sizes[1]);
    }
}