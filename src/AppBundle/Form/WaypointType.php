<?php


namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use libphonenumber\PhoneNumberFormat;

class WaypointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('attr' => array('placeholder' => 'Nom de votre établissement'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ce champ est requis")),
                )
            ))
            ->add('phone', PhoneNumberType::class, array('attr' => array('placeholder' => 'Téléphone Ex : +33601234567'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ce champ est requis")),
                )
            ))
            ->add('email', EmailType::class, array('attr' => array('placeholder' => 'Votre adresse email'),
                'constraints' => array(
                    new NotBlank(array("message" => "Please provide a valid email")),
                    new Email(array("message" => "Your email doesn't seems to be valid")),
                )
            ))
            ->add('address', TextareaType::class, array('attr' => array('placeholder' => 'L\'adresse de votre institution'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ce champ est requis")),
                )
            ))
            ->add('description', TextareaType::class, array('attr' => array('placeholder' => 'Déscription de votre établissement'),
                'constraints' => array(
                    new NotBlank(array("message" => "Ce champ est requis")),
                )
            ))
            ->add('lat', TextType::class)
            ->add('lon', TextType::class)
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    public function getName()
    {
        return 'waypoint_form';
    }
}