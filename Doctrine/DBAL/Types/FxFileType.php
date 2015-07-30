<?php

namespace Felicast\Bundle\FxFileStoreBundle\Doctrine\DBAL\Types;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;
use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
use Felicast\Bundle\FxFileStoreBundle\Service\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FxFileType extends JsonArrayType
{
    const FX_FILE = 'fx_file';

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value !== null) {
            if (!($value instanceof FxFile)) {
                throw new \InvalidArgumentException('Must be FxFile');
            }
            $fxFile = $value;
            $file = $fxFile->getFile();
            if ($file instanceof UploadedFile) {
                $this->uploader->upload($fxFile);
            }
            $value = array(
                'realFilename' => $fxFile->getRealFilename(),
                'filename' => $fxFile->getFilename(),
                'mimeType' => $fxFile->getMimeType(),
                'size' => $fxFile->getSize(),
                'extension' => $fxFile->getExtension(),
            );
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);
        if (($value === null) || (!isset($value['realFilename']))) {
            return null;
        }
        $filePath = $this->uploader->getPath($value['realFilename']);
        return new FxFile($filePath, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::FX_FILE;
    }

    public function setUploder(Uploader $uploader)
    {
        $this->uploader = $uploader;

        return $this;
    }
}
