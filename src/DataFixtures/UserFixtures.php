<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');


        $userAdmin1 = new User();
        $password = $this->hasher->hashPassword($userAdmin1, 'password');

        $userAdmin1->setUsername('val')
            ->setEmail('val@gmail.com')
            ->setPassword($password)
            ->setRoles(['ROLE_USER'])
            ->setBirthday(new \DateTime('1998-07-19 17:04:13'))
            ->setLocation("Dans mon Bureau");

        $manager->persist($userAdmin1);

        $userAdmin2 = new User();
        $password = $this->hasher->hashPassword($userAdmin2, 'password');

        $userAdmin2->setUsername('jerome')
            ->setEmail('jerome@gmail.com')
            ->setPassword($password)
            ->setRoles(['ROLE_USER'])
            ->setBirthday(new \DateTime('1979-06-28 21:20:34'))
            ->setLocation("Avec mon chat");

        $manager->persist($userAdmin2);


        for ($i = 1; $i <= 8; $i++) {
            $user = new User();
            $password = $this->hasher->hashPassword($user, 'password');

            $user->setUsername($faker->firstName($gender = null|'male'|'female') )
                ->setEmail($faker->email())
                ->setPassword($password)
                ->setRoles(['ROLE_USER'])
                ->setCreatedAt(new \DateTime())
                ->setBirthday($faker->dateTimeBetween('-60 years', '-18 years'))
                ->setLocation($faker->city());

            $manager->persist($user);
        }

        $manager->flush();
    }
}

