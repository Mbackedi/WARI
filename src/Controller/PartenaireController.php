<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;

use App\Repository\ProfilRepository;
use JMS\Serializer\SerializerInterface;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class PartenaireController extends AbstractController
{


    /**
     * @Route("/partenaire", name="partenaire_new", methods={"GET" ,"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();

            return new Response('Partenaire ajouté', Response::HTTP_CREATED);
        }

        return new Response('Veuillez renseigner les champs ', Response::HTTP_CREATED);
    }


    /**
     * @Route("/listerpartenaire/{id}", name="list_phone", methods={"GET"})
     */
    public function listerpartenaire(PartenaireRepository $partenaireRepository, SerializerInterface $serializer)
    {
        $partenaire = $partenaireRepository->findAll();
        $data = $serializer->serialize($partenaire, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }



    /**
     * @Route("/listerprofil/{id}", name="list_profil", methods={"GET"})
     */
    public function listerprofil(ProfilRepository $profilRepository, SerializerInterface $serializer)
    {
        $profil = $profilRepository->findAll();
        $data = $serializer->serialize($profil, 'json');

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }



    /**
     * @Route("/{id}", name="partenaire_show", methods={"GET"})
     */
    public function show(Partenaire $partenaire): Response
    {
        return $this->render('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="partenaire_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Partenaire $partenaire): Response
    {
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('partenaire_index');
        }

        return $this->render('partenaire/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partenaire_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Partenaire $partenaire): Response
    {
        if ($this->isCsrfTokenValid('delete' . $partenaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($partenaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('partenaire_index');
    }
}
