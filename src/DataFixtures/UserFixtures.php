<?php

namespace App\DataFixtures;

use App\Entity\UserAccounts\Permission;
use App\Entity\UserAccounts\Role;
use App\Entity\UserAccounts\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void{

        $userRole = new Role();
        $userRole->setRoleName("Administrator");

        $permission = new Permission();
        $permission->setRole($userRole);
        $permission->setActions(["view","add","edit","delete"]);
        $permission->setObject("App\Controller\UserAccounts\RoleController");

        $user = new User();
        $user->setFirstName("System");
        $user->setMiddleName("");
        $user->setLastName("Admin");
        $user->setAccountStatus("A");
        $user->setEmail("michael@me.com");
        $user->setPassword($this->hasher->hashPassword($user,"123456"));
        $user->addRole($userRole);

        $manager->persist($userRole);
        $manager->persist($permission);
        $manager->persist($user);
        $manager->flush();
    }
}
