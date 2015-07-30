<?php

namespace Felicast\Bundle\FxFileStoreBundle\Twig;

use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
use Felicast\Bundle\FxFileStoreBundle\Service\Uploader;

class FxFileStoreExtension extends \Twig_Extension
{
    private $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('fx_file_url', array($this, 'fileUrl')),
            new \Twig_SimpleFunction('fx_image_thumbnail_url', array($this, 'imageThumbnailUrl')),
        );
    }

    public function fileUrl($file)
    {
        return $this->uploader->getUrl($file);
    }

    /**
     * @param FxFile|string $file
     * @param int $width
     * @param int $height
     * @return string
     */
    public function imageThumbnailUrl($file, $width = 0, $height = 0)
    {
        return $this->uploader->getImageThumbnailUrl($file, $width, $height);
    }

    public function getName()
    {
        return 'fx_file_storage';
    }
}
