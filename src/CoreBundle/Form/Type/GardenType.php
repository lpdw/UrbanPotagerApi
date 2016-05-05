<?php

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class GardenType extends AbstractType
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
            ->add('isPublic', CheckboxType::class)
            ->add('latitude', NumberType::class, [
                'scale' => 2,
            ])
            ->add('longitude', NumberType::class, [
                'scale' => 2,
            ])
            ->add('show_location', CheckboxType::class)
            ->add('country', TextType::class)
            ->add('city', TextType::class)
            ->add('zip_code', TextType::class)
            ->add('address1', TextType::class)
            ->add('address2', TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'CoreBundle\Entity\Garden',
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // TODO change ?
    }
}
