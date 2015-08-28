<?php

namespace Felicast\Bundle\FxFileStoreBundle\Service;

use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetsManager
{
    private $mappings;

    private function generateFileName(File $file, $extension)
    {
        return sha1_file($file->getPathname()) . '.' . $extension;
    }

    public function __construct($mappings)
    {
        $this->mappings = $mappings;
    }

    public function upload(FxFile $file)
    {
        $realFile = $file->getFile();
        if ($realFile instanceof UploadedFile) {
            $fileDir = $this->getUploadDir();
            $fileName = $this->generateFileName($realFile, $realFile->getClientOriginalExtension());
            $newFile = $realFile->move($fileDir, $fileName);
            $file->setFile($newFile);

            //set data from client side
            $file->setFilename($realFile->getClientOriginalName());
            $file->setMimeType($realFile->getClientMimeType());
            $file->setExtension($realFile->getClientOriginalExtension());
        }
    }

    public function move(File $file)
    {
        $fileDir = $this->getUploadDir();
        $fileName = $this->generateFileName($file, $file->getExtension());
        $newFile = $file->move($fileDir, $fileName);
        return new FxFile($newFile);
    }


    private function normalizeFilename($filenameOrFile)
    {
        $filename = null;
        if ($filenameOrFile instanceof FxFile) {
            $filename = $filenameOrFile->getRealFilename();
        } elseif (is_array($filenameOrFile) && isset($filenameOrFile['realFilename'])) {
            $filename = $filenameOrFile['realFilename'];
        } elseif (is_string($filenameOrFile)) {
            $filename = $filenameOrFile;
        }
        return $filename;
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

    public function getPath($filenameOrFile)
    {
        $filename = $this->normalizeFilename($filenameOrFile);
        if (!is_string($filename)) {
            return null;
        }

        return $this->getUploadDir() . DIRECTORY_SEPARATOR . $filename;
    }

    public function getImageThumbnailPath($filenameOrFile, $width, $height)
    {
        $filename = $this->normalizeFilename($filenameOrFile);
        if (!is_string($filename)) {
            return null;
        }

        $directory = $this->getUploadDir() . DIRECTORY_SEPARATOR . $this->getThumbnailDir($width, $height);
        if (!is_dir($directory) && @mkdir($directory, 0777, true) === false) {
            throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
        }
        return $directory . DIRECTORY_SEPARATOR . $filename;
    }

    public function getUrl($filenameOrFile)
    {
        $filename = $this->normalizeFilename($filenameOrFile);
        if (!is_string($filename)) {
            return null;
        }

        return $this->getUrlBase() . '/' . $filename;
    }

    public function getImageThumbnailUrl($filenameOrFile, $width = 0, $height = 0)
    {
        $filename = $this->normalizeFilename($filenameOrFile);
        if (!is_string($filename)) {
            return null;
        }

        $width = max((int)($width), 0);
        $height = max((int)($height), 0);

        if (($width === 0) && ($height === 0)) {
            return $this->getUrl($filename);
        }
        $filePath = $this->getImageThumbnailPath($filename, $width, $height);
        if (!file_exists($filePath)) {
            try {
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
            } catch (\Exception $e) {

            }
        }

        return $this->getUrlBase() . '/' . $this->getThumbnailDir($width, $height) . '/' . $filename;
    }

    /**
     * @param $fileUrl
     * @return FxFile
     */
    public function uploadFileFromRemoteServer($fileUrl)
    {
        $tempFileName = tempnam(sys_get_temp_dir(), 'fxUpload');
        if (copy($fileUrl, $tempFileName) === false) {
            $errors = error_get_last();
            throw new \RuntimeException("Can not copy file from $fileUrl to $tempFileName : {$errors['message']}");
        }
        $realFile = new File($tempFileName);
        $newFileName = $this->generateFileName($realFile, pathinfo($fileUrl, PATHINFO_EXTENSION));
        $newPath = $this->getPath($newFileName);
        if (!file_exists($newPath)) {
            if (rename($tempFileName, $newPath) === false) {
                $errors = error_get_last();
                throw new \RuntimeException("Can not move file from $tempFileName to $newPath : {$errors['message']}");
            }
        }
        return new FxFile($newPath);
    }
}
