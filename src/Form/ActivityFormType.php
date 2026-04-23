<?php


namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = $builder->getData();
        $tags = $activity->getTags() ? array_combine(array_values($activity->getTags()), array_values($activity->getTags())) : [];

        $builder
            ->add('imageFiles', CollectionType::class, [
                'label'         => false,
                'entry_type'    => ImageFileType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'mapped'        => false,
                'by_reference'  => false,
                'entry_options' => ['label' => false],
                'attr'          => ['id' => 'photo-collection'],
            ])
            ->add('title', TextType::class, [
                'label' => "Titre de l'Activité",
                'required' => false,
                'constraints' => [
                    new NotBlank(
                        message: 'Ecrivez un titre',
                    ),
                    new Length(
                        min: 4,
                        max: 40,
                        minMessage: 'Your Titre should be at least {{ limit }} characters',
                    ),
            ]])
            ->add('description', TextareaType::class, [
                'label' => "Description de l'Activité",
                'required' => false,
            ])
            ->add('location', TextType::class, [
                "label" => "Lieu de l'Activité",
                'required' => false,
            ])
            /** @todo
             * champ des heures + date
             */
            ->add('date', DateType::class, [
                "label" => "Date de l'Activité",
                'widget' => 'choice',
                'format' => 'dd MM yyyy HH:mm',
                "placeholder" => "Select",
                'required' => false,
            ])
            ->add('spot', IntegerType::class, [
                "label" => "Nombre de places disponibles",
                'required' => false,
            ])
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

            if (!isset($formData['tags'])) return;

            $tags = $formData['tags'];

            $form->add('tags', ChoiceType::class, [
                'required' => false,
                'label' => 'Tags',
                'multiple' => true,
                'choices' => array_combine(array_values($tags), array_values($tags)),
                'attr' => [
                    'class' => 'select-cactus select2-tags',
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

