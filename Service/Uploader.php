<?php

namespace Felicast\Bundle\FxFileStoreBundle\Service;

use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
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
            $fileDir = isset($this->mappings['upload_dir']) ? $this->mappings['upload_dir'] : 'upload';
            $fileName = sha1_file($realFile->getPathname()) . '.' . $realFile->getClientOriginalExtension();
            $newFile = $realFile->move($fileDir, $fileName);
            $file->setFile($newFile);

            //set data from client side
            $file->setFilename($realFile->getClientOriginalName());
            $file->setMimeType($realFile->getClientMimeType());
            $file->setExtension($realFile->getClientOriginalExtension());
        }
    }

    public function getPath($filename)
    {
        $fileDir = isset($this->mappings['upload_dir']) ? $this->mappings['upload_dir'] : 'upload';
        return $fileDir . DIRECTORY_SEPARATOR . $filename;
    }

    public function getUrl($filename)
    {
        $fileDir = isset($this->mappings['upload_path']) ? $this->mappings['upload_path'] : '/upload';
        return $fileDir . '/' . $filename;
    }
}
