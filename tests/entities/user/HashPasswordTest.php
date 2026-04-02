<?php

namespace App\Tests\entities\user;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPasswordTest extends KernelTestCase
{
    public function testHasherPassword(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $plainPassword = 'Test12345';

        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $this->assertEquals(true, $passwordHasher->isPasswordValid($user, $plainPassword));
    }
}
