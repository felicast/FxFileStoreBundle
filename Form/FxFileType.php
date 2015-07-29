<?php

namespace Felicast\Bundle\FxFileStoreBundle\Form;

use Felicast\Bundle\FxFileStoreBundle\Form\DataTransformer\FxFileDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FxFileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FxFileDataTransformer());

//        $builder->addEventListener(
//            FormEvents::PRE_SUBMIT,
//            function (FormEvent $formEvent) {
//                $data = $formEvent->getData();
//                if (is_string($data)) {
//                    $meta = json_decode($data, true);
//                    if ($meta && isset($meta['type']) && isset($meta['realFilename'])) {
//                        $data = new MFile(null, $this->uploader->getPath($meta['type'], $meta['realFilename']), $meta);
//                        $formEvent->setData($data);
//                    }
//                }
//            }
//        );
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



