<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Form\CompteUserType;
use Doctrine\ORM\EntityManager;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class CompteController extends AbstractController
{
    /**
     * @Route("/compte", name="compte_index", methods={"POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        //  $form = $this->createForm(CompteUserType::class, $user);
        // recuperation de tous les donner
        $data = $request->request->all();
        // Test Valider du ninea
        $test = $this->getDoctrine()->getRepository(Partenaire::class)->findOneBy([
            'NINEA' => $data['ninea']
        ]);
        // si le ninea est valid
        if ($test) {
            // lid du partenaire est de type int et il doit etre un objet pour que la migration se face
            // on utilisa la methode find qui nous revoi un objet
            $rien = $this->getDoctrine()->getRepository(Partenaire::class)->find($test->getId());
            $compte = new Compte();

            $compte->setSolde(0);
            $num = rand(1000000000, 9999999999);
            $sn = "SN";
            $number = $sn . $num;
            $compte->setNumCompte($number);
           /*  $compte->setSolde(0);
            $compte->setNumCompte("5631616311"); */
            // Apres recuperation de lobjet on le met dans le setter
            $compte->setPartenaire($rien);
            $entityManager->persist($compte);
            $entityManager->flush();
            $data = [
                'status1' => 201,
                'message1' => 'Le compte a été créé'
            ];

            return new JsonResponse($data, 201);
        }
        $data = [
            'status1' => 201,
            'message1' => 'bakhoul'
        ];

        return new JsonResponse($data, 201);


        // $form->handleRequest($request);
        //  $form->submit($data);



    }



    /**
     *@Route("/listercompte", name="listCompte", methods={"GET"})
     */
    public function listercompte(CompteRepository $compteRepository, SerializerInterface $serializer)
    {
        $compte = $compteRepository->findAll();
        $data = $serializer->serialize($compte, 'json', ['groups' => ['liste-compte']]);

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }



    /**
     * @Route("/new", name="compte_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('compte_index');
        }

        return $this->render('compte/new.html.twig', [
            'compte' => $compte,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="compte_show", methods={"GET"})
     */
    public function show(Compte $compte): Response
    {
        return $this->render('compte/show.html.twig', [
            'compte' => $compte,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="compte_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Compte $compte): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('compte_index');
        }

        return $this->render('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="compte_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Compte $compte): Response
    {
        if ($this->isCsrfTokenValid('delete' . $compte->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compte_index');
    }
}
