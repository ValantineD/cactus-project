<?php


namespace App\Form;

use App\Entity\Activity;
use App\Entity\Theme;
use App\Services\GeocodingService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;

class ActivityFormType extends AbstractType
{

    public function __construct(
        private readonly Packages         $assets,
        private readonly GeocodingService $geocodingService
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = $builder->getData();
        $tags = $activity->getTags() ? array_combine(array_values($activity->getTags()), array_values($activity->getTags())) : [];

        $builder
            ->add('imageFiles', CollectionType::class, [
                'label' => false,
                'entry_type' => ImageFileType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'mapped' => false,
                'by_reference' => false,
                'entry_options' => ['label' => false],
                'attr' => ['id' => 'photo-collection'],
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
            ->add('themes', EntityType::class, [
                'label' => false,
                'class' => Theme::class,
                'choice_label' => "title",
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'id' => 'activity-themes',
                    'class' => 'select-custom-themes',
                ],
                'choice_attr' => function (Theme $theme) {
                    return [
                        'data-icon' => $this->assets->getUrl($theme->getIconFilename())
                    ];
                },
                'constraints' => [
                    new Count(
                        min: 1,
                        max: 2,
                        minMessage: 'Veuillez sélectionner au moins 1 thème.',
                        maxMessage: 'Vous ne pouvez sélectionner que 2 thèmes maximum.'
                    ),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description de l'Activité",
                'required' => false,
                'attr' => [
                    'rows' => 8,
                ],
            ])
            ->add('location', TextType::class, [
                'required' => false,
                'attr' => [
                    'autocomplete' => 'street-address',
                    'placeholder' => '13 Rue de la République, Marseille'
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Ecrivez adcqsxdvqsgbjdh',
                    ),
                ]])
            ->add('date_start', DateTimeType::class, [
                'label' => "Date de début de l'Activité",
                'widget' => 'choice',
                'html5' => false,
                'input' => 'datetime',
                'placeholder' => [
                    'day' => 'Jour',
                    'month' => 'Mois',
                    'year' => 'Année',
                    'hour' => 'Heure',
                    'minute' => 'Minute',
                ],
                'years' => range(date('Y'), date('Y') + 10),
                'hours' => range(0, 23),
                'minutes' => range(0, 59, 15),
                'with_seconds' => false,
                'required' => false,
                'constraints' => [
                    new NotNull(message: 'La date de début ne peut pas être vide.'),
                    new GreaterThanOrEqual([
                        'value' => new \DateTime('today'),
                        'message' => 'La date de début ne peut pas être dans le passé.',
                    ]),
                ],
            ])
            ->add('date_end', DateTimeType::class, [
                "label" => "Date de fin de l'Activité",
                'widget' => 'choice',
                'html5' => false,
                'input' => 'datetime',
                'placeholder' => [
                    'day' => 'Jour',
                    'month' => 'Mois',
                    'year' => 'Année',
                    'hour' => 'Heure',
                    'minute' => 'Minute',
                ],
                'years' => range(date('Y'), date('Y') + 10),
                'hours' => range(0, 23),
                'minutes' => range(0, 59, 15),
                'with_seconds' => false,
                'required' => false,
                'constraints' => [
                    new NotNull(message: 'La date de fin ne peut pas être vide.'),
                    new GreaterThanOrEqual([
                        'value' => new \DateTime('today'),
                        'message' => 'La date de fin ne peut pas être dans le passé.',
                    ]),
                ],
            ])
            ->add('spot', IntegerType::class, [
                "label" => "Nombre de places disponibles",
                'required' => false,
                'attr' => [
                    'min' => 2,
                    'max' => 200,
                    'step' => 1,
                    'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                ],
                'constraints' => [
                    new Range(
                        min: 2,
                        max: 200,
                        notInRangeMessage: 'Le nombre de places doit être entre 2 et 200.',
                    ),
                ],
            ])
            ->add('tags', ChoiceType::class, [
                'required' => false,
                'label' => 'Tags',
                'multiple' => true,
                'choices' => $tags,
                'attr' => [
                    'class' => 'select-custom-tags',
                ],
            ])
            ->add('savePublish', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    "class" => "btn btn-cactus-primary"
                ]
            ])
            ->add('saveDraft', SubmitType::class, [
                'label' => 'Enregistrer en Brouillon',
                'attr' => [
                    "class" => "btn btn-cactus-secondary"
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
                    'class' => 'select-custom-tags',
                ],
            ]);

        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
            $form = $event->getForm();
            /** @var Activity $activity */
            $activity = $event->getData();

            $dateStart = $activity->getDateStart();
            $dateEnd = $activity->getDateEnd();

            if ($dateStart && $dateEnd && $dateEnd <= $dateStart) {
                $form->get('date_end')->addError(
                    new FormError(
                        'La date de fin ne peut pas être avant la date de début.'
                    )
                );
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
            $form = $event->getForm();
            /** @var Activity $activity */
            $activity = $event->getData();

            $coords = $this->geocodingService->geocode($activity->getLocation());

            if ($coords === null) {
                $form->get('location')->addError(
                    new FormError('Adresse introuvable.')
                );

                return;
            }

            $activity->setLatitude($coords['lat']);
            $activity->setLongitude($coords['lng']);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }


}

