<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Compte;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ApiFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {

        $compte = new Compte();
        $num = rand(1000000000, 9999999999);
        $sn = "SN";
        $number = $sn . $num;
        $compte->setNumCompte($number);
        $user = new User();
        $user->setUsername('Kabirou');
        $mdp = "123456";
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $password = $this->encoder->encodePassword($user, $mdp);
        $user->setPassword($password);
        $user->setNomcomplet('Mbodj');
        $user->setTelephone(76129635);
        $user->setImageName("image.jpg");
        $user->setPartenaire(null);
        $user->setProfil(null);
        $user->setStatut("debloquer");
        $user->setUpdatedAt(new \DateTime('now'));


        $user->setCompte(null);
        $manager->persist($user);
        $manager->flush();
    }
}
