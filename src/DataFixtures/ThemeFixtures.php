<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $themes = [
            ['title' => 'Animaux', 'icon' => 'images/themes/pets.svg'],
            ['title' => 'Art', 'icon' => 'images/themes/art.svg'],
            ['title' => 'Artisanat', 'icon' => 'images/themes/handicraft.svg'],
            ['title' => 'Boissons', 'icon' => 'images/themes/drinks.svg'],
            ['title' => 'Compétition', 'icon' => 'images/themes/competition.svg'],
            ['title' => 'Cuisine', 'icon' => 'images/themes/cooking.svg'],
            ['title' => 'Culture G', 'icon' => 'images/themes/language-academia.svg'],
            ['title' => 'Ecriture', 'icon' => 'images/themes/writings.svg'],
            ['title' => 'Espace', 'icon' => 'images/themes/astronomy.svg'],
            ['title' => 'Films', 'icon' => 'images/themes/film.svg'],
            ['title' => 'Jeux de Société', 'icon' => 'images/themes/cards_boardgames.svg'],
            ['title' => 'Jeux Vidéos', 'icon' => 'images/themes/videogame.svg'],
            ['title' => 'Lecture', 'icon' => 'images/themes/books.svg'],
            ['title' => 'Mer', 'icon' => 'images/themes/sea.svg'],
            ['title' => 'Musées', 'icon' => 'images/themes/museum.svg'],
            ['title' => 'Musique', 'icon' => 'images/themes/music.svg'],
            ['title' => 'Photos', 'icon' => 'images/themes/photo.svg'],
            ['title' => 'Plein-Air', 'icon' => 'images/themes/outside.svg'],
            ['title' => 'Repas', 'icon' => 'images/themes/food.svg'],
            ['title' => 'RPG', 'icon' => 'images/themes/rpg.svg'],
            ['title' => 'Sport', 'icon' => 'images/themes/sport.svg'],
            ['title' => 'Théâtre', 'icon' => 'images/themes/theatre.svg'],
            ['title' => 'Voyage', 'icon' => 'images/themes/travel.svg'],
        ];

        foreach ($themes as $data) {
            $theme = new Theme();
            $theme->setTitle($data['title'])
                 ->setIconFilename($data['icon']);

            $manager->persist($theme);
        }

        $manager->flush();
    }
}


