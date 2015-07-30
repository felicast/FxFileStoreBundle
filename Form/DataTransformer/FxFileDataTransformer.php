<?php


namespace Felicast\Bundle\FxFileStoreBundle\Form\DataTransformer;

use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
use Felicast\Bundle\FxFileStoreBundle\Service\Uploader;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;


class FxFileDataTransformer implements DataTransformerInterface
{
    /**
     * @var Uploader
     */
    private $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function transform($fileDataFromDb)
    {
        return $fileDataFromDb;
    }

    public function reverseTransform($data)
    {
        if (isset($data['file'])) {
            return new FxFile($data['file']);
        } elseif (isset($data['meta'])) {
            $meta = json_decode($data['meta'], true);
            if ($meta && isset($meta['realFilename'])) {
                return new FxFile($this->uploader->getPath($meta['realFilename']));
            }
        }
        throw new \InvalidArgumentException('Must be File or FxFile');
    }
}
