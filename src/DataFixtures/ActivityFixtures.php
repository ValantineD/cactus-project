<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Theme;
use App\Entity\User;
use App\Enum\EnumState;
use App\Enum\EnumStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ActivityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $users = $manager->getRepository(User::class)->findAll();


        $allThemes = $manager->getRepository(Theme::class)->findAll();
        $themeMap = [];
        foreach ($allThemes as $theme) {
            $themeMap[$theme->getTitle()] = $theme;
        }

        $activitiesData = [
            [
                'title' => 'Soirée jeux de société',
                'location' => 'Marseille, 13001',
                'description' => 'Une soirée conviviale autour de jeux de société classiques et modernes. Tous niveaux bienvenus !',
                'tags' => ['jeux', 'soirée', 'convivial'],
                'spot' => 8,
                'dateStart' => new \DateTime('+7 days 19:00'),
                'dateEnd' => new \DateTime('+7 days 23:00'),
                'latitude' => 43.2965,
                'longitude' => 5.3698,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['Jeux de Société'],
            ],
            [
                'title' => 'Randonnée calanques',
                'location' => 'Calanque de Morgiou, Marseille',
                'description' => 'Randonnée de niveau intermédiaire dans les calanques. Prévoir de bonnes chaussures et de l\'eau.',
                'tags' => ['nature', 'sport', 'plein-air'],
                'spot' => 12,
                'dateStart' => new \DateTime('+3 days 08:00'),
                'dateEnd' => new \DateTime('+3 days 13:00'),
                'latitude' => 43.2140,
                'longitude' => 5.4197,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['Plein-Air', 'Mer'],
            ],
            [
                'title' => 'Atelier aquarelle débutants',
                'location' => 'Cours Julien, Marseille',
                'description' => 'Initiation à la peinture aquarelle. Matériel fourni sur place. Aucune expérience requise.',
                'tags' => ['art', 'peinture', 'atelier'],
                'spot' => 6,
                'dateStart' => new \DateTime('+10 days 14:00'),
                'dateEnd' => new \DateTime('+10 days 17:00'),
                'latitude' => 43.2907,
                'longitude' => 5.3810,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['Art'],
            ],
            [
                'title' => 'Club de lecture SF',
                'location' => 'Bibliothèque de l\'Alcazar, Marseille',
                'description' => 'Discussion autour du roman "Fondation" d\'Isaac Asimov. Lecture préalable recommandée mais pas obligatoire.',
                'tags' => ['lecture', 'SF', 'discussion'],
                'spot' => 10,
                'dateStart' => new \DateTime('+14 days 18:30'),
                'dateEnd' => new \DateTime('+14 days 20:30'),
                'latitude' => 43.2969,
                'longitude' => 5.3763,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['Lecture'],
            ],
            [
                'title' => 'Sortie photo coucher de soleil',
                'location' => 'Corniche Kennedy, Marseille',
                'description' => 'Séance photo collective au coucher du soleil sur la corniche. Tous appareils acceptés, même les smartphones.',
                'tags' => ['photo', 'balade', 'coucher de soleil', 'mer'],
                'spot' => 15,
                'dateStart' => new \DateTime('+5 days 19:30'),
                'dateEnd' => new \DateTime('+5 days 21:30'),
                'latitude' => 43.2738,
                'longitude' => 5.3529,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['Photos', 'Plein-Air'],
            ],
            [
                'title' => 'Tournoi Mario Kart',
                'location' => 'Marseille, 13004',
                'description' => 'Tournoi amical Mario Kart sur Switch. 4 consoles disponibles, 16 joueurs max. Coupes et défis surprise au programme.',
                'tags' => ['jeux vidéos', 'tournoi', 'Nintendo'],
                'spot' => 16,
                'dateStart' => new \DateTime('+2 days 15:00'),
                'dateEnd' => new \DateTime('+2 days 19:00'),
                'latitude' => 43.3027,
                'longitude' => 5.3947,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['Jeux Vidéos'],
            ],
            [
                'title' => 'Brunch découverte',
                'location' => 'Le Panier, Marseille',
                'description' => 'Brunch collectif dans le vieux quartier du Panier. Chacun apporte une spécialité à partager.',
                'tags' => ['repas', 'brunch', 'partage'],
                'spot' => 20,
                'dateStart' => new \DateTime('+21 days 10:30'),
                'dateEnd' => new \DateTime('+21 days 13:00'),
                'latitude' => 43.2994,
                'longitude' => 5.3680,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['Repas', 'Boissons'],
            ],
            [
                'title' => 'Session RPG Donjons & Dragons',
                'location' => 'Marseille, 13006',
                'description' => 'Partie de D&D 5e pour joueurs confirmés. Scénario inédit, personnages pré-tirés disponibles si besoin.',
                'tags' => ['RPG', 'D&D', 'jeux de rôle'],
                'spot' => 5,
                'dateStart' => new \DateTime('+4 days 17:00'),
                'dateEnd' => new \DateTime('+4 days 22:00'),
                'latitude' => 43.2884,
                'longitude' => 5.3796,
                'state' => EnumState::OPEN,
                'status' => EnumStatus::PUBLISHED,
                'themes' => ['RPG'],
            ],
        ];

        foreach ($activitiesData as $data) {
            $activity = new Activity();
            $activity
                ->setTitle($data['title'])
                ->setLocation($data['location'])
                ->setDescription($data['description'])
                ->setTags($data['tags'])
                ->setSpot($data['spot'])
                ->setDateStart($data['dateStart'])
                ->setDateEnd($data['dateEnd'])
                ->setLatitude($data['latitude'])
                ->setLongitude($data['longitude'])
                ->setState($data['state'])
                ->setStatus($data['status'])
                ->setCreatedAt(new \DateTime())
                ->setUser($users[array_rand($users)]);

            foreach ($data['themes'] as $themeTitle) {
                if (isset($themeMap[$themeTitle])) {
                    $activity->addTheme($themeMap[$themeTitle]);
                }
            }

            $manager->persist($activity);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ThemeFixtures::class,
        ];
    }
}
