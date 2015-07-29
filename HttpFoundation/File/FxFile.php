<?php

namespace Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File;


use Symfony\Component\HttpFoundation\File\File;

class FxFile
{
    /** @var File */
    private $file;
    private $path;

    private $realFilename;
    private $filename;
    private $mimeType;
    private $size;
    private $extension;

    private function initByPath($path)
    {
        $this->path = $path;
    }

    private function initByMetadata(array $metadata)
    {
        $this->realFilename = isset($metadata['realFilename']) ? $metadata['realFilename'] : null;
        $this->filename = isset($metadata['filename']) ? $metadata['filename'] : null;
        $this->size = isset($metadata['size']) ? $metadata['size'] : null;
        $this->mimeType = isset($metadata['mimeType']) ? $metadata['mimeType'] : null;
        $this->extension = isset($metadata['extension']) ? $metadata['extension'] : null;
    }

    private function initByFile(File $file)
    {
        $this->file = $file;
    }

    private function clearMeta()
    {
        $this->realFilename = null;
        $this->filename = null;
        $this->size = null;
        $this->mimeType = null;
        $this->extension = null;
    }

    public function __construct($fileOrPath, array $metadata = array())
    {
        if ($fileOrPath instanceof File) {
            $this->initByFile($fileOrPath);
        } elseif (is_string($fileOrPath)) {
            $this->initByPath($fileOrPath);
        } else {
            throw new \InvalidArgumentException('Must by filename, metadata or file');
        }
        $this->initByMetadata($metadata);
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return FxFile
     */
    public function setFile($file)
    {
        $this->file = $file;

        $this->clearMeta();

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if ($this->path === null) {
            $this->path = $this->getFile()->getPathname();
        }
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getRealFilename()
    {
        if ($this->realFilename === null) {
            $this->realFilename = $this->getFile()->getFilename();
        }
        return $this->realFilename;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        if ($this->filename === null) {
            $this->filename = $this->getFile()->getFilename();
        }
        return $this->filename;
    }

    /**
     * @param mixed $filename
     * @return FxFile
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        if ($this->mimeType === null) {
            $this->mimeType = $this->getFile()->getMimeType();
        }
        return $this->mimeType;
    }

    /**
     * @param mixed $mimeType
     * @return FxFile
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        if ($this->size === null) {
            $this->size = $this->getFile()->getSize();
        }
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        if ($this->extension === null) {
            $this->extension = $this->getFile()->getExtension();
        }
        return $this->extension;
    }

    /**
     * @param mixed $extension
     * @return FxFile
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }
}
