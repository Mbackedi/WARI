<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Tarif;
use App\Entity\Transaction;
use App\Form\TransactionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Kernel;
use SymfonyBundles\BundleDependency\Tests\Kurnel;

/**
 * @Route("/api")
 */
class TransactionController extends AbstractController
{



    /**
     * @Route("/envoie", name="envoie", methods={"POST"}) 
     * 
     */
    public function envoie(Request $request, EntityManagerInterface $entityManager)
    {
        // AJOUT OPERATION
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);
        $values = $request->request->all();
        $form->submit($values);

        if ($form->isSubmitted()) {

            $transaction->setDatedenvoie(new \DateTime());
            //generation du code
            $c = 1;
            $r = rand(10000000, 99999999);
            $codes = $c . $r;
            $transaction->setCode($codes);

            //numero transaction
            $trans = rand(11111111, 99999999);
            $transaction->setNumerotransacion($trans);

            // recuperer l'id du caissier
            $user = $this->getUser();
            $transaction->setCaissier($user);

            // recuperer la valeur du frais
            $repository = $this->getDoctrine()->getRepository(Tarif::class);
            $commission = $repository->findAll();

            //recuperer la valeur du montant saisie
            $montant = $transaction->getMontant();

            //Verifier le montant dispo
            $comptes = $this->getUser()->getCompte();
           // var_dump($comptes); die();
            if ($transaction->getMontant() >= $comptes->getSolde()) {
                return $this->json([
                    'message1' => 'votre solde( ' . $comptes->getSolde() . ' ) ne vous permez pas d\'effectuer cette transaction'
                ]);
            }

            // verifier les frais  correspondant au montant
            foreach ($commission as $values) {
                $values->getBorneInf();
                $values->getBorneSup();
                $values->getValeur();

                if ($montant >= $values->getBorneInf() &&  $montant <= $values->getBorneSup()) {
                    $valeur = $values->getValeur();
                }
            }

            $transaction->setFrais($valeur);

            // repartition des commissions 
            $sup = ($valeur * 40) / 100;
            $part = ($valeur * 20) / 100;
            $etat = ($valeur * 30) / 100;
            // $retrait = ($values->getValeur() * 10) / 100;

            // dimunition du monatnt envoyé au niveau du solde et ajout de la commission pour sup
            $comptes->setSolde($comptes->getSolde() - $transaction->getMontant() + $sup);

            $transaction->setCommissionsup($sup);
            $transaction->setCommissionparte($part);
            $transaction->setCommissionetat($etat);
            //$transaction->setCommissionRetrait($retrait);

            $total = $montant + $valeur;
            $transaction->setTotal($total);
            $transaction->setTypedoperation('envoyer');

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($transaction);
            $entityManager->flush();

            $data = [
                'status1' => 201,
                'message1' => 'L\'envoie  a été effectué'
            ];
            return new JsonResponse($data, 201);
        }

        $data = [
            'status2' => 500,
            'message2' => 'ERREUR, VERIFIER LES DONNÉES SAISIES'
        ];
        return new JsonResponse($data, 500);
    }


    /**
     * @Route("/retrait", name="retrait", methods={"POST"}) 
     * 
     */

    public function retrait(Request $request, EntityManagerInterface $entityManager)
    {

        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);
        $values = $request->request->all();
        $form->submit($values);
        $codes = $transaction->getCode();

        $repository = $this->getDoctrine()->getRepository(Transaction::class);
        $cod = $repository->findOneBy(['code' => $codes]);

        if (!$cod) {
            return new Response('Ce code est invalide', Response::HTTP_CREATED);
        }

        $type = $cod->getTypedoperation();


        if ($cod->getCode() == $codes && $type == "retiré") {
            return new Response('code est dejas retiré', Response::HTTP_CREATED);
        }

        $user = $this->getUser();
        $cod->setCaissierBen($user);
        $cod->setTypedoperation("retiré");
        $cod->setDateretrait(new \DateTime());
        $cod->setNumeropieceBen($values['numeropieceBen']);
        $cod->setTypepieceBen($values['typepieceBen']);
        $entityManager->persist($cod);
        $entityManager->flush();
        return new Response('Retrait effectué', Response::HTTP_CREATED);
    }




    /**
     * @Route("/document", name="document")
     */

    public function index()
    {
        // Configurez Dompdf selon vos besoins
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instancier Dompdf avec nos options
        $dompdf = new Dompdf($pdfOptions);

        // Récupère le code HTML généré dans notre fichier twig
        $html = $this->renderView('transaction/index.html.twig', [
            'title' => "Welcome to our PDF Test"
        ]);

        // Charger du HTML dans Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        //Exporter le PDF généré dans le navigateur (vue intégrée)
        $dompdf->stream("testpdf.pdf", [
            "Attachment" => false
        ]);
    }



    /**
     * @Route("/{id}", name="transaction_show", methods={"GET"})
     */
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Transaction $transaction): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Transaction $transaction): Response
    {
        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transaction_index');
    }
}
