<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profil;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;

/**
 * @Route("/api")
 */

class SecurityController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /** 
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        if (isset($values->username, $values->password)) {
            $user = new User();
            $user->setUsername($values->username);
            $mdp = "12345678";
            $user->setPassword($passwordEncoder->encodePassword($user, $mdp));

            // recuperer id profil
            $repos = $this->getDoctrine()->getRepository(Profil::class);
            $profils = $repos->find($values->profil);
            $user->setProfil($profils);

            $role = [];
            if ($profils->getLibelle() == "admin") {
                $role = (["ROLE_ADMIN"]);
            } elseif ($profils->getLibelle() == "user") {
                $role = (["ROLE_USER"]);
            } elseif ($profils->getLibelle() == "caissier") {
                $role = (["ROLE_CAISSIER"]);
            } elseif ($profils->getLibelle() == "superadmin") {
                $role = (["ROLE_SUPER_ADMIN"]);
            }

            $user->setRoles($role);
            $user->setNomcomplet($values->nomcomplet);
            $user->setTelephone($values->telephone);
            $block = "debloquer";
            $user->setStatut($block);



            // recuperer l'id du partenaire
            $repo = $this->getDoctrine()->getRepository(Partenaire::class);
            $partenaires = $repo->find($values->partenaire);

            $user->setPartenaire($partenaires);

            $entityManager->persist($user);
            $entityManager->flush();

            $data = [
                'status1' => 201,
                'message1' => 'L\'utilisateur a été créé'
            ];

            return new JsonResponse($data, 201);
        }
        $data = [
            'status2' => 500,
            'message2' => 'Vous devez renseigner les clés username et password'
        ];
        return new JsonResponse($data, 500);
    }



    /** 
     * @Route("/login", name="login", methods={"POST"})
     * @param JWTEncoderInterface $JWTEncoder
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function login(Request $request, JWTEncoderInterface $JWTEncoder)
    {


        $values = json_decode($request->getContent());
        $username   = $values->username; // json-string
        $password   = $values->password; // json-string

        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(['username' => $username]);

        if (!$user) {
            return $this->json([
                'message' => 'Username incorrect'
            ]);
        }

        $isValid = $this->passwordEncoder
            ->isPasswordValid($user, $password);
        if (!$isValid) {
            return $this->json([
                'message' => 'Mot de passe incorect'
            ]);
        }
        if ($user->getStatut() == "bloquer") {
            return $this->json([
                'message' => 'ACCÈS REFUSÉ VOUS ETES BLOQUER !'
            ]);
        }


        if ($user->getStatut() != null && $user->getPartenaire() != null && $user->getPartenaire()->getStatut() == "bloquer") {
            return $this->json([
                'message1' => 'ACCES REFUSÉ VOTRE PARTENAIRE  ' . $user->getPartenaire()->getNomEntreprise() . ' est bloqué'
            ]);
        }

        $token = $JWTEncoder->encode([
            'roles' => $user->getRoles(),
            'username' => $user->getUsername(),
            'exp' => time() + 86400 // 1 day expiration
        ]);

        return $this->json([
            'token' => $token
        ]);
    }


    /**
     * @Route("/users/bloquer", name="userBlock", methods={"GET","POST"})
     * @Route("/users/debloquer", name="userDeblock", methods={"GET","POST"})
     */
    public function userBloquer(Request $request, UserRepository $userRepo, EntityManagerInterface $entityManager): Response
    {
        $values = json_decode($request->getContent());
        $user = $userRepo->findOneByUsername($values->username);
        //echo $user->getStatut();

        if ($user->getUsername() == "Kabirou") {

            return $this->json([
                'message1' => 'VOUS NE POUVEZ PAS BLOQUER LE SUPER ADMIN !'
            ]);
        } elseif ($user->getStatut() == "bloquer") {
            $user->setStatut("debloquer");
            $entityManager->flush();

            return $this->json([
                'message1' => $user->getUsername() . "  vous etes débloqué"
            ]);
        } else {
            $user->setStatut("bloquer");
            $entityManager->flush();
            return $this->json([
                'message1' => $user->getUsername() . "  vous etes bloqué"
            ]);
        }
    }
}
