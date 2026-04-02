<?php

namespace App\Tests\entities\user;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegexUsernameTest extends KernelTestCase
{
    public function testValidUsername(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $user = new User();
        $user->setUsername('User123');
        $user->setPassword('Password123');
        $user->setEmail('user123@gmail.com');


        $error = $container->get('validator')->validate($user);
        $this->assertEquals(0, $error->count());
    }

    public function testInvalidUsername(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $user = new User();
        $user->setUsername('User 123,;:!<>^$&é(-è_çà)+=');
        $user->setPassword('Password123');
        $user->setEmail('user123@gmail.com');


        $error = $container->get('validator')->validate($user);
        $this->assertEquals(1, $error->count());
    }

}
