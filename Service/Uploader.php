<?php

namespace Felicast\Bundle\FxFileStoreBundle\Service;

use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    private $mappings;

    public function __construct($mappings)
    {
        $this->mappings = $mappings;
    }

    public function upload(FxFile $file)
    {
        $realFile = $file->getFile();
        if ($realFile instanceof UploadedFile) {
            $fileDir = $this->getUploadDir();
            $fileName = sha1_file($realFile->getPathname()) . '.' . $realFile->getClientOriginalExtension();
            $newFile = $realFile->move($fileDir, $fileName);
            $file->setFile($newFile);

            //set data from client side
            $file->setFilename($realFile->getClientOriginalName());
            $file->setMimeType($realFile->getClientMimeType());
            $file->setExtension($realFile->getClientOriginalExtension());
        }
    }

    public function getUploadDir()
    {
        return isset($this->mappings['upload_dir']) ? $this->mappings['upload_dir'] : 'upload';
    }

    public function getThumbnailDir($width, $height)
    {
        $width = (int)($width);
        $height = (int)($height);

        return $width . '_' . $height;
    }

    public function getUrlBase()
    {
        return isset($this->mappings['upload_path']) ? $this->mappings['upload_path'] : '/upload';
    }

    public function getPath($filename)
    {
        return $this->getUploadDir() . DIRECTORY_SEPARATOR . $filename;
    }

    public function getImageThumbnailPath($filename, $width, $height)
    {
        $directory = $this->getUploadDir() . DIRECTORY_SEPARATOR . $this->getThumbnailDir($width, $height);
        if (!is_dir($directory) && @mkdir($directory, 0777, true) === false) {
            throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
        }
        return $directory . DIRECTORY_SEPARATOR . $filename;
    }

    public function getUrl($filename)
    {
        return $this->getUrlBase() . '/' . $filename;
    }

    public function getImageThumbnailUrl($filename, $width = 0, $height = 0)
    {
        $width = max((int)($width), 0);
        $height = max((int)($height), 0);

        if (($width === 0) && ($height === 0)) {
            return $this->getUrl($filename);
        }
        $filePath = $this->getImageThumbnailPath($filename, $width, $height);
        if (!file_exists($filePath)) {
            $thumb = new \Imagick($this->getPath($filename));
            $newWidth = $width;
            $newHeight = $height;
            if ($newWidth === 0) {
                $originalWidth = $thumb->getImageWidth();
                $originalHeight = $thumb->getImageHeight();
                $ratio = $originalHeight / $newHeight;
                $newWidth = $originalWidth / $ratio;
            }
            if ($newHeight === 0) {
                $originalWidth = $thumb->getImageWidth();
                $originalHeight = $thumb->getImageHeight();
                $ratio = $originalWidth / $newWidth;
                $newHeight = $originalHeight / $ratio;
            }
            $thumb->cropThumbnailImage($newWidth, $newHeight);

            $thumb->writeImage($filePath);
            $thumb->destroy();
        }

        return $this->getUrlBase() . '/' . $this->getThumbnailDir($width, $height) . '/' . $filename;
    }
}
