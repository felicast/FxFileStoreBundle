<?php
namespace Felicast\Bundle\FxFileStoreBundle\Doctrine\ORM\Mapping;

use Doctrine\ORM\Mapping\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class Uploadable implements Annotation
{
    /**
     * @var string
     */
    public $type;
}
