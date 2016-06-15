<?php

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use CoreBundle\Entity\Type;

class MeasureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', NumberType::class, [
                'scale' => 2,
            ])
            ->add('type', EntityType::class, [
                'class' => 'CoreBundle:Type',
                'choice_value' => function(Type $type) {
                    return $type->getSlug();
                }
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CoreBundle\Entity\Measure',
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // TODO change ?
    }
}
