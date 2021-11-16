<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Form\CreationSortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/edit_sortie/{id}", name="edit_sortie")
     */
    public function editSortie(Request $request, Sortie $sortie): Response
    {
        $formSortie = $this->createForm(CreationSortieType::class, $sortie);
        $formSortie->handleRequest($request);
        if ($formSortie->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->render('main/creationsortie.html.twig', [
                'sortie' => $sortie,
                'success_ajout' => 'null']);
        }
        return $this->render('main/creationsortie.html.twig', [
            'formSortie' => $formSortie->createView(),
            'success_ajout' => 'null',
        ]);
    }

    /**
     * @Route("create_sortie", name="app_sortie")
     */
    public function creationSortie(Request $request, EtatRepository $etatRepository, SortieRepository $repo, LieuRepository $lieuRepository, SiteRepository $siteRepository, VilleRepository $villeRepository): Response
    {
        $sortie = new Sortie();
        $rue = null;
        $cp = null;
        $lieuChoisi = null;

        $lieuCat = $lieuRepository->findAll();
        $villeRepo = $villeRepository->findAll();

        $organisteur = $this->getUser();

        $formSortie = $this->createForm(CreationSortieType::class, $sortie);
        $formSortie->handleRequest($request);

        if (isset($_POST['action'])) {
            if ($request->request->get('titre-sortie') != null){
                $sortie->setNom($request->request->get('titre-sortie')) ;
            }

            if ($request->request->get('date-h-sortie') != null){
                $timeSortie = new \DateTime($request->request->get('date-h-sortie'));
                $timeSortie->format('Y-m-d H:i:s');
                $sortie->setDateHeureDebut($timeSortie);
            }

            if ($request->request->get('date-l-sortie') != null){
                $timeInscription = new \DateTime($request->request->get('date-l-sortie'));
                $timeInscription->format('Y-m-d H:i:s');
                $sortie->setDateLimiteInscription($timeInscription) ;
            }

            if ($request->request->get('nbParticipant-sortie') != null){
                $sortie->setNbInscriptionsMax($request->request->get('nbParticipant-sortie')) ;
            }

            if ($request->request->get('duree-sortie') != null){
                $sortie->setDuree($request->request->get('duree-sortie')) ;
            }


                $sortie->setInfosSortie($request->request->get('desc-sortie')) ;


            if ($request->request->get('lieu-select') != null){
                $idLieu = $request->request->get('lieu-select');
                $lieuChoisi = $lieuRepository->findOneBy([
                        "id" => $idLieu
                    ]);
                $sortie->setLieu($lieuChoisi);
            }


            $sortie->setOrganisateur($organisteur);
            $sortie->setSite($organisteur->getSite());

            $etat = $etatRepository->find($_POST['action']);
            $sortie->setEtat($etat);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        } else {

        }


        return $this->render('main/creationsortie.html.twig', [
            'sortie' => $sortie,
            'organisateur' => $organisteur,
            'lieux' => $lieuCat,
            'ville' => $villeRepo,
            'rue' => $rue,
            'cp' => $cp,
            'siteOrganisateur' => $organisteur->getSite()->getNom(),
            'formSortie' => $formSortie->createView(),
        ]);
    }

    /**
     * @Route("/api/select_lieu/{id}", name="api_ville" ,methods={"GET"})
     */
    public function selectLieu(LieuRepository $lieuRepo, VilleRepository $villeRepository, $id): Response
    {
        $ville = $villeRepository->findOneBy(
            [
                "id" => $id
            ]
        );


        $lieux = $lieuRepo->LieuFiltre($ville);

        $tabLieux = array();
        $compteur=0;
        foreach ($lieux as $l){
            $tabLieux[$compteur]["id"] = $l->getId();
            $tabLieux[$compteur]["nom"] = $l->getNom();
            $compteur++;
        }

        return $this->json($tabLieux);
    }

    /**
     * @Route("/api/info_lieu/{id}", name="api_lieux" ,methods={"GET"})
     */
    public function infoLieu(LieuRepository $lieuRepo, VilleRepository $repo, $id): Response
    {
        $lieu = $lieuRepo->findOneBy(
            [
                "id" => $id
            ]
        );

        $tabLieu = array();
        $tabLieu[0] = $lieu->getRue();
        $tabLieu[1] = $lieu->getVille()->getCodePostal();
        $tabLieu[2] = $lieu->getLatitude();
        $tabLieu[3] = $lieu->getLongitude();

        //dd($tabLieu);

        //$rue = $lieu->getRue();
        //$cp = $lieu->getVille()->getCodePostal();

        return $this->json($tabLieu);
    }

}