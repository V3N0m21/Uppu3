<?php
namespace Uppu3\Helper;
class Resize
{

    private $image;
    private $width;
    private $height;
    private $imageResized;
    private $mime;

    public function resizeFile($fileName, $path)
    {
        if (!$this->image = $this->openImage($fileName)) {
            throw new Exception("File '$fileName' not an image", 1);
        } else {

            $this->width = imagesx($this->image);
            $this->height = imagesy($this->image);

            $this->resizeImage(150, 150);
            $this->saveImage($path, 80);
        }
    }

    private function openImage($file)
    {
        $img = getimagesize($file);
        if (!$img) {
            $img = false;
        } else {
            $this->mime = $img['mime'];

            switch ($this->mime) {
                case 'image/jpeg':
                    $img = imagecreatefromjpeg($file);
                    break;

                case 'image/gif':
                    $img = imagecreatefromgif($file);
                    break;

                case 'image/png':
                    $img = imagecreatefrompng($file);
                    break;

                default:
                    $img = false;
                    break;
            }
        }
        return $img;
    }

    private function resizeImage($newWidth, $newHeight)
    {
        if ($this->width < $newWidth && $this->height < $newHeight) {
            $newWidth = $this->width;
            $newHeight = $this->height;
        }
        $optionArray = $this->getDimensions($newWidth, $newHeight);
        $optimalWidth = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];

        $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);
    }

    private function getDimensions($newWidth, $newHeight)
    {

        $optionArray = $this->getSizeByAuto($newWidth, $newHeight);
        $optimalWidth = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    private function getSizeByAuto($newWidth, $newHeight)
    {
        if ($this->height < $this->width) {
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);
        } elseif ($this->height > $this->width) {
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;
        } else {
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
            } elseif ($newHeight > $newWidth) {
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;
            } else {
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
            }
        }
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    private function getSizeByFixedHeight($newHeight)
    {
        $ratio = $this->width / $this->height;
        $newWidth = ceil($newHeight * $ratio);
        return $newWidth;
    }

    private function getSizeByFixedWidth($newWidth)
    {
        $ratio = $this->height / $this->width;
        $newHeight = ceil($newWidth * $ratio);
        return $newHeight;
    }

    private function saveImage($savePath, $imageQuality = "100")
    {
        switch ($this->mime) {
            case 'image/jpeg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->imageResized, $savePath, $imageQuality);
                } else {
                    throw new Exception("File '$savePath' not created", 1);
                }
                break;

            case 'image/gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->imageResized, $savePath);
                } else {
                    throw new Exception("File '$savePath' not created", 1);
                }
                break;

            case 'image/png':

                // *** Scale quality from 0-100 to 0-9
                $scaleQuality = round(($imageQuality / 100) * 9);

                // *** Invert quality setting as 0 is best, not 9
                $invertScaleQuality = 9 - $scaleQuality;

                if (imagetypes() & IMG_PNG) {
                    imagepng($this->imageResized, $savePath, $invertScaleQuality);
                } else {
                    throw new Exception("File '$savePath' not created", 1);
                }
                break;

            default:
                throw new Exception("File '$savePath' not an image", 1);
                break;
        }

        imagedestroy($this->imageResized);
    }
}
