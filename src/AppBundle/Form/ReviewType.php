<?php

namespace AppBundle\Form;

use AppBundle\Entity\Review;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class ReviewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("note", ChoiceType::class, array(
                'label' => "Note",
                'choices' => array(
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true
            ))
            ->add("commentaire", TextareaType::class, array(
                'label' => "Commentaire",
                'attr' => array('placeholder' => 'Votre commentaire ...'),
                'required' => true
            ))
            ->add("roadtripId", HiddenType::class, array(
                'label' => "Roadtrip"
            ))
            ->add("userId", HiddenType::class, array(
                'label' => "User"
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Envoyer mon avis',
                'attr' => array(
                    'class' => 'full-width blue lighten-1',
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
            'data_class' => 'AppBundle\Entity\Review'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'review';
    }
}