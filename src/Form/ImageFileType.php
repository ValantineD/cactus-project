<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ImageFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImageFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label'       => false,
                'required'    => false,
                'constraints' => [new Assert\File(
                    extensions: ['jpg', 'jpeg', 'png'],
                    extensionsMessage: 'Please upload a valid picture'
                ),
            ]])
            ->add('position', IntegerType::class, [
                'constraints' => [
                    new Length(
                        min: 1,
                        max: 5,
                    ),
            ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImageFile::class,
        ]);
    }
}
