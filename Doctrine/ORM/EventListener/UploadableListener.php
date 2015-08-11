<?php

namespace Felicast\Bundle\FxFileStoreBundle\Doctrine\ORM\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Felicast\Bundle\FxFileStoreBundle\Doctrine\DBAL\Types\FxFileType;
use Felicast\Bundle\FxFileStoreBundle\Service\AssetsManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UploadableListener implements EventSubscriber
{
    /** @var AssetsManager */
    private $uploader;

    public function __construct(ContainerInterface $container, AssetsManager $uploader)
    {
        $this->uploader = $uploader;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        foreach ($classMetadata->getFieldNames() as $fieldName) {
            $type = $classMetadata->getTypeOfField($fieldName);
            if ($type === FxFileType::FX_FILE) {
                /** @var FxFileType $type */
                $type = Type::getType($type);
                $type->setUploder($this->uploader);
            }
        }
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
        );
    }
}
