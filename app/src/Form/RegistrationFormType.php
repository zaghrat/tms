<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Vorname'
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Nachname'
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Passwort',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Bitte Passwort eingeben',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Ihr Passwort sollte mindestens {{ limit }} Zeichen lang sein',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
