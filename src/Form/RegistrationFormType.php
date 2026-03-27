<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('username', TextType::class, [
                "label" => "Nom d'utilisateur",
                "attr" => [
                    "placeholder" => "Nom d'utilisateur",
                    "autocomplete" => "off"
                ]
            ])
            ->add('email', EmailType::class, [
                "label" => "Adresse e-mail",
                "attr" => [
                        "placeholder" => "adresse mail",
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                "label" => "Mot de Passe",
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    "placeholder" => "mot de passe",
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 6,
                        max: 4096,
                        minMessage: 'Your password should be at least {{ limit }} characters',
                    ),
                ],
            ])
            ->add("birthday", DateType::class, [
                "label" => "Date d'anniversaire",
                'widget' => 'single_text',
                'html5'  => false,
                'format' => 'dd/MM/yyyy'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
