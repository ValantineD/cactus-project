<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profileFilename', HiddenType::class, [
                "attr" => [
                    "class" => "input-main-img"
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'Profil Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File(
                        extensions: ['jpg', 'jpeg', 'png'],
                        extensionsMessage: 'Please upload a valid picture'
                    ),
                ]
            ])
            ->add('username')
            ->add('email')
            ->add('location')
            ->add("birthday", BirthdayType::class, [
                "label" => "Date d'anniversaire",
                'widget' => 'choice',
                'format' => 'dd MM yyyy',
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    "class" => "btn btn-cactus-primary"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
