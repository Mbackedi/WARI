<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Entity\Envoyeur;
use App\Entity\Profil;
use App\Form\UserType;
use App\Form\CompteType;
use App\Entity\Partenaire;
use App\Form\BeneficiaireType;
use App\Form\PartenaireType;
use App\Repository\UserRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/user", name="user_new", methods={"GET","POST"})
     */

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        $values = $request->request->all();
        $form->submit($values);
        $files = $request->files->all()['imageName'];

        if ($form->isSubmitted()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setImageFile($files);
            // recuperer id profil
            $repos = $this->getDoctrine()->getRepository(Profil::class);
            $profils = $repos->find($values['profil']);
            $user->setProfil($profils);
            /*   //recuperer id du compte
            $repos = $this->getDoctrine()->getRepository(Compte::class);
            $compte = $repos->find($values['compte']);
            $user->setCompte($compte); */

            $role = [];
            if ($profils->getLibelle() == "SUPER_ADMIN") {
                $role = (["ROLE_ADMIN"]);
            } elseif ($profils->getLibelle() == "ADMIN") {
                $role = (["ROLE_USER"]);
            } elseif ($profils->getLibelle() == "USER") {
                $role = (["ROLE_USER"]);
            } elseif ($profils->getLibelle() == "CAISSIER") {
                $role = (["ROLE_CAISSIER"]);
            }
            $user->setRoles($role);
            $user->setStatut("debloquer");

            // recuperer l'id du partenaire
            $users = $this->getUser()->getPartenaire();
            $user->setPartenaire($users);


            $entityManager = $this->getDoctrine()->getManager();
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
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    // Ajouter les 3 en meme temps


    /**
     * @Route("/admin", name="admin_utilisateur_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, SerializerInterface $serializer): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $data = $request->request->all();
        $form->submit($data);
        $partenaire->setStatut("debloquer");
        $entityManager->persist($partenaire);
        $entityManager->flush();
        //recuperation de l id du partenaire//
        $repository = $this->getDoctrine()->getRepository(Partenaire::class);
        $part = $repository->find($partenaire->getId());

        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $data = $request->request->all();
        $form->submit($data);
        $compte->setSolde(0);
        $num = rand(1000000000, 9999999999);
        $sn = "SN";
        $number = $sn . $num;
        $compte->setNumCompte($number);
        $compte->setPartenaire($partenaire);
        $entityManager = $this->getDoctrine()->getManager();

        $utilisateur = new User();
        $form = $this->createForm(UserType::class, $utilisateur);
        $form->handleRequest($request);
        $files = $request->files->all()['imageName'];
        $form->submit($data);
        $utilisateur->setImageFile($files);

        $utilisateur->setRoles(["ROLE_CAISSIER"]);
        $utilisateur->setPartenaire($partenaire);
        $utilisateur->setStatut("debloquer");
        $utilisateur->setPassword(
            $passwordEncoder->encodePassword(
                $utilisateur,
                $form->get('plainPassword')->getData()
            )

        );
        $errors = $validator->validate($utilisateur);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'content_type' => 'application/json'
            ]);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($compte);
        $entityManager->persist($utilisateur);
        $entityManager->flush();
        return new Response('Admin  ajouté  avec succès', Response::HTTP_CREATED);
    }
}
