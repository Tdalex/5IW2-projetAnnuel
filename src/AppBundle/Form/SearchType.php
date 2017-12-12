<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('addressDep', TextType::class, array('label' => 'Adresse de départ', 'attr' =>
                array(
                    'class' => 'autocomplete',
                    'id' => 'addressDep',
                    'name' => 'addressDep'
                )
            ))
            ->add('addressDes', TextType::class, array('label' => 'Adresse de départ', 'attr' =>
                array(
                    'class' => 'autocomplete',
                    'id' => 'addressDep',
                    'name' => 'addressDep'
                )
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'mapped' => false
        ));
    }


}
