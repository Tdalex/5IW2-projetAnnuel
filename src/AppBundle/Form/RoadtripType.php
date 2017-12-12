<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\StopType;

class RoadtripType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stopStart', StopType::class, array(
                'label' => 'Départ de'
            ))
            ->add('stopEnd', StopType::class, array(
                'label' => 'Destination'
            ))
            ->add('title', TextType::class, array(
                'label' => 'Titre du roadtrip'
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description du roadtrip'
            ))
            ->add('stops', StopType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Roadtrip'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_roadtrip';
    }


}
