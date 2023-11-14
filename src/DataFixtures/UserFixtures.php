<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;


class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        
        $user = new User();
        $email = "exemple@exemple.com";
        $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => $email]);
        if(!$existingUser){
        $currentDate = date('Y-m-d H:i:s');

        $user->setEmail($email);
        $user->setUsername("Student");
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setCreatedAt($currentDate);
        $user->setUpdatedAt($currentDate);

        $manager->persist($user);
        $manager->flush();}
    }
}
