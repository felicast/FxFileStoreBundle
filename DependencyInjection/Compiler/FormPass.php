<?php


namespace  Felicast\Bundle\FxFileStoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;


class FormPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $resources = $container->getParameter('twig.form.resources');
        $resources[] = 'FxFileStoreBundle:Form:fields.html.twig';

        $container->setParameter('twig.form.resources', $resources);
    }
}
