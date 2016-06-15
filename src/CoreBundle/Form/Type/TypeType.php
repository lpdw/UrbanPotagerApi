<?php

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use CoreBundle\Entity\Type;

class TypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('min', NumberType::class, [
                'scale' => 2,
            ])
            ->add('max', NumberType::class, [
                'scale' => 2,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'plant' => Type::PLANT,
                    'sensor' => Type::SENSOR,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CoreBundle\Entity\Type',
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // TODO change ?
    }
}
