<?php

namespace AppBundle\Form;

use AppBundle\Entity\Stop;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('duration', IntegerType::class, array(
                'label' => 'Durée du roadtrip (en jours)'
            ))
            ->add('stops', CollectionType::class, array(
                'entry_type' => StopType::class,
                'label' => 'Etapes',
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
                'attr' => array(
                    'class' => 'stop-collection',
                ),
                'delete_empty' => function (Stop $stop = null) {
                    return null === $stop || empty($stop->getAddress());
                },
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Enregistrer mon roadtrip',
                'attr' => array(
                    'class' => 'full-width green lighten-3',
                )
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Roadtrip',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'roadtrip';
    }


}
