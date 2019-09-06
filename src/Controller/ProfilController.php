<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil", methods={"POST"})
     */

    public function new(Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $values = json_decode($request->getContent());
        $user = new Profil();
        $user->setLibelle($values->libelle);
        $entityManager->persist($user);
        $entityManager->flush();
        $data = [
            'status1' => 201,
            'message1' => '\'profil créé'
        ];

        return new JsonResponse($data, 201);

        $data = [
            'status2' => 500,
            'message2' => 'Vous devez renseigner les clés username et password'
        ];
        return new JsonResponse($data, 500);
    }
}
