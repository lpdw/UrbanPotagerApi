<?php

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CollectionAccessType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('access', CollectionType::class, [
                'entry_type' => AccessType::class,
                'allow_add' => true,
                //'allow_delete' => true,
                //'delete_empty' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CoreBundle\Model\CollectionAccess'
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // TODO change ?
    }
}
