<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Form\CreationSortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
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
     * @Route("/creation_sortie", name="app_sortie")
     */
    public function creationSortie(Request $request,EtatRepository $etatRepository, SortieRepository $repo, LieuRepository $lieuRepository, SiteRepository $siteRepository): Response
    {
        $sortie = new Sortie();
        $formSortie = $this->createForm(CreationSortieType::class, $sortie);
        $formSortie->handleRequest($request);
        $organisteur = $this->getUser();
        $lieu = $lieuRepository->find(1);
        $site = $siteRepository->find(1);
        $etat = $etatRepository->find('1');


        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            $sortie->setOrganisateur($organisteur);
            $sortie->setEtat($etat);
            $sortie->setNom();
            $sortie->setDateHeureDebut();
            $sortie->setDuree();
            $sortie->setNbInscriptionsMax();
            $sortie->setInfosSortie();
            $sortie->setDateLimiteInscription();
            $sortie->setSite($site);
            $sortie->setLieu($lieu);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('main');
        }

        return $this->render('main/creationsortie.html.twig', [
            'organisateur'=>$organisteur,
            'formSortie' => $formSortie->createView(),
        ]);
    }
}
