<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, [
            'label' => 'Prenom'
        ]);

        $builder->add('lastName', TextType::class, [
            'label' => 'Nom de famille'
        ]);

        $builder->add('email', EmailType::class, [
            'label' => 'Email'
        ]);

        $builder->add('birthdate', DateType::class, array(
            'label' => 'Date de naissance',
            'attr' => ['class' => 'datepicker'],
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd',
        ));

        $builder->add($builder->create('gender', ChoiceType::class, [
            'label'    => 'Genre',
            'choices'  => [
                'Homme'   => 'MALE',
                'Femme' => 'FEMALE',
            ],
            'multiple' => false,
            'expanded' => true,
            'required' => true
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\User',
            'validation_groups'  => 'UserEdit',
            'attr'               => [
                'id' => 'form-' . $this->getBlockPrefix()
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user';
    }


}
