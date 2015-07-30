<?php

namespace Felicast\Bundle\FxFileStoreBundle\Form;

use Felicast\Bundle\FxFileStoreBundle\Form\DataTransformer\FxFileDataTransformer;
use Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile;
use Felicast\Bundle\FxFileStoreBundle\Service\Uploader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FxFileType extends AbstractType
{
    /** @var Uploader */
    private $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FxFileDataTransformer($this->uploader));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'compound' => false,
                'data_class' => 'Felicast\Bundle\FxFileStoreBundle\HttpFoundation\File\FxFile',
                'empty_data' => null,
                'multiple' => false,
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';
            $view->vars['attr']['multiple'] = 'multiple';
        }
        if (isset($view->vars['value'])) {
            $meta = null;
            $value = $form->getData();
            if (!($value instanceof FxFile)) {
                $value = $view->vars['value'];
            }
            if ($value instanceof FxFile) {
                $meta = json_encode($value);
            } elseif (isset($value['meta'])) {
                $meta = $value['meta'];
            }
            if (is_string($meta)) {
                $meta = json_decode($meta, true);
            }
            $view->vars['meta'] = $meta;
        }

        $view->vars = array_replace(
            $view->vars,
            array(
                'type' => 'file',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['multipart'] = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fx_file';
    }
}



