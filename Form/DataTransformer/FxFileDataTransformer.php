<?php


namespace Felicast\Bundle\FxFileStoreBundle\Form\DataTransformer;

use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;


class FxFileDataTransformer implements DataTransformerInterface
{
    public function transform($fileDataFromDb)
    {
        return $fileDataFromDb;
    }

    public function reverseTransform($file)
    {
        if ($file instanceof FxFile) {
            return $file;
        }
        if ($file instanceof File) {
            return new FxFile($file);
        }
        if ($file === null) {
            return $file;
        }
        throw new \InvalidArgumentException('Must be File or FxFile');
    }
}
