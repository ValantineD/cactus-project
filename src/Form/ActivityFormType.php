<?php


namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = $builder->getData();
        $tags = $activity->getTags() ? array_combine(array_values($activity->getTags()), array_values($activity->getTags())) : [];

        $builder
            ->add('title')
            ->add('description')
            ->add('images', FileType::class, [
                'label' => 'Image Activity',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File(
                        extensions: ['jpg', 'jpeg', 'png'],
                        extensionsMessage: 'Please upload a valid picture'
                    ),
                ]])
            ->add('location')
            ->add('date', DateType::class, [
                "label" => "Date de l'Activité",
                'widget' => 'choice',
                'format' => 'dd MM yyyy HH:mm',
            ])
            ->add('spot')
            ->add('tags', ChoiceType::class, [
                'required' => false,
                'label' => 'Tags',
                'multiple' => true,
                'choices' => $tags,
                'attr' => [
                    'class' => 'select2-tags',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    "class" => "btn btn-cactus-primary"
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event) {
            $form = $event->getForm();
            $formData = $event->getData();

            $tags = $formData['tags'];

            $form->add('tags', ChoiceType::class, [
                'required' => false,
                'label' => 'Tags',
                'multiple' => true,
                'choices' => array_combine(array_values($tags), array_values($tags)),
                'attr' => [
                    'class' => 'select2-tags',
                ],
            ]);

        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}

