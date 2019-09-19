<?php

namespace Pepsite\Service;

use InvalidArgumentException;
use RuntimeException;

class ImageManager
{
    public const AVATAR_DIR = './data/avatar/';
    public const AVATAR_WIDTH = 50;
    public const AVATAR_HEIGHT = 50;

    public function getBase64Data($imageName, $type = IMAGETYPE_JPEG) : ?string
    {
        $image = $this->open($imageName, $type);
        ob_start();
        imagejpeg($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        return base64_encode($imageData);
    }

    public function saveImage($imageData) : ?string
    {
        $imageName = $imageData['tmp_name'];
        if (!(is_readable($imageName) and is_file($imageName))) {
            return null;
        }
        $resImage = $this->resize($imageName);
        $resName = uniqid();
        if (!is_dir(self::AVATAR_DIR)) {
            if (!mkdir(self::AVATAR_DIR, 0755, true)) {
                throw new RuntimeException('Error creating directory ' . self::AVATAR_DIR);
            }
        }
        $resFileName = self::AVATAR_DIR . $resName;
        if (!imagejpeg($resImage, $resFileName, 95)) {
            return null;
        }
        imagedestroy($resImage);
        return $resName;
    }

    private function open($imageName, $type = IMAGETYPE_JPEG)
    {
        switch ($type) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imageName);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imageName);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imageName);
                break;
            default:
                throw new InvalidArgumentException("Image type {$type} not supported");
        }
        return $image;
    }

    private function resize($imageName)
    {
        list($width, $height, $type) = getimagesize($imageName);
        $image = $this->open($imageName, $type);

        if ($width === self::AVATAR_WIDTH and $height === self::AVATAR_HEIGHT) {
            return $image;
        }

        if ($width > $height) {
            $src_x = round(($width - $height) / 2);
            $src_y = 0;
            $src_wh = $height;
        } elseif ($height > $width) {
            $src_x = 0;
            $src_y = round(($height - $width) / 2);
            $src_wh = $width;
        } else {
            $src_x = $src_y = 0;
            $src_wh = $width;
        }

        $temp = imagecreatetruecolor(self::AVATAR_WIDTH, self::AVATAR_HEIGHT);
        imagecopyresampled(
            $temp,
            $image,
            0,
            0,
            $src_x,
            $src_y,
            self::AVATAR_WIDTH,
            self::AVATAR_HEIGHT,
            $src_wh,
            $src_wh
        );
        imagedestroy($image);
        return $temp;
    }
}
