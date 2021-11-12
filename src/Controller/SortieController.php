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
     * @Route("/create_sortie", name="app_sortie") method=post
     */
    public function creationSortie( Request $request,EtatRepository $etatRepository, SortieRepository $repo, LieuRepository $lieuRepository, SiteRepository $siteRepository, VilleRepository $villeRepository): Response
    {
        $sortie = new Sortie();
        $rue = null;
        $cp = null;
        $lieuChoisi = null;
        if(isset($_POST['lieu-select'])) {
            $lieuChoisi = $_POST['lieu-select'];
            $rue = $lieuChoisi->getRue();
            $cp = $lieuChoisi->getVille()->getCodePostal();
        } else {
        }

        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
        if (isset($_POST['enregistrer'])) {

            $etat = $etatRepository->find(1);

        } elseif (isset($_POST['creer'])) {

            $etat = $etatRepository->find(2);

        } else {

        }
        $organisteur = $this->getUser();

        $formSortie = $this->createForm(CreationSortieType::class, $sortie);
        $formSortie->handleRequest($request);


        if ($formSortie->isSubmitted()){
            $sortie->setOrganisateur($organisteur);
            $sortie->setEtat($etat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render('main/creationsortie.html.twig', [
            'sortie' => $sortie,
            'organisateur'=>$organisteur,
            'lieux' => $lieu,
            'ville' => $ville,
            'rue' => $rue,
            'cp' => $cp,
            'siteOrganisateur' => $organisteur->getSite()->getNom(),
            'formSortie' => $formSortie->createView(),
        ]);
    }

    /**
     * @Route("/create_sortie", name="sortie_lieu") method=get
     */
    public function formLieu( Request $request,EtatRepository $etatRepository, SortieRepository $repo, LieuRepository $lieuRepository, SiteRepository $siteRepository, VilleRepository $villeRepository): Response
    {
        $sortie = new Sortie();
        $rue = null;
        $cp = null;
        $lieuChoisi = null;
        if(isset($_POST['lieu-select'])) {
            $lieuChoisi = $_POST['lieu-select'];
            $rue = $lieuChoisi->getRue();
            $cp = $lieuChoisi->getVille()->getCodePostal();
        } else {
        }

        $lieu = $lieuRepository->findAll();
        $ville = $villeRepository->findAll();
        if (isset($_POST['enregistrer'])) {

            $etat = $etatRepository->find(1);

        } elseif (isset($_POST['creer'])) {

            $etat = $etatRepository->find(2);

        } else {

        }
        $organisteur = $this->getUser();

        $formSortie = $this->createForm(CreationSortieType::class, $sortie);
        $formSortie->handleRequest($request);


        if ($formSortie->isSubmitted()){
            $sortie->setOrganisateur($organisteur);
            $sortie->setEtat($etat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render('main/creationsortie.html.twig', [
            'sortie' => $sortie,
            'organisateur'=>$organisteur,
            'lieux' => $lieu,
            'ville' => $ville,
            'rue' => $rue,
            'cp' => $cp,
            'siteOrganisateur' => $organisteur->getSite()->getNom(),
            'formSortie' => $formSortie->createView(),
        ]);
    }
}
