<?php

namespace CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use CoreBundle\Entity\Alert;
use CoreBundle\Entity\Type;

class AlertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('threshold', NumberType::class, [
                'scale' => 2,
            ])
            ->add('comparison', ChoiceType::class, [
                'choices' => Alert::$OPERATOR,
            ])
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('message', TextType::class)
            ->add('type', EntityType::class, [
                'class' => 'CoreBundle:Type',
                'choice_value' => function(Type $type = null) {
                    return (!is_null($type)) ? $type->getSlug() : '';
                }
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\Alert'
        ));
    }

    public function getBlockPrefix()
    {
        return ''; // TODO change ?
    }
}
