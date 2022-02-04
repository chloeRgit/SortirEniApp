<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(SortieRepository $repo, SiteRepository $repoSite): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sorties=$repo->findAll();
        $user=$this->getUser();
        $sites=$repoSite->findAll();

        return $this->render('main/index.html.twig', [
            'sorties'=>$sorties,
            'user'=>$user,
            'sites'=>$sites,
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/recherche", name="recherche")
     * @param SortieRepository $repo
     * @param SiteRepository $repoSite
     * @param Request $request
     *
     * @return Response
     */
    public function recherche(SortieRepository $repo, SiteRepository $repoSite, Request $request): Response
    {   $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //récuparation des paramètres
        $user=$this->getUser();
        $sites=$repoSite->findAll();
        $siteresp = $request->request->get('site-select');
        $nameresp = $request->request->get('name-filter');
        $datedebresp = $request->request->get('date-deb');
        $datefinresp = $request->request->get('date-fin');
        $organisateurresp = $request->request->get('organisateur-filter');
        $inscritresp = $request->request->get('inscrit-filter');
        $noninscritresp = $request->request->get('noninscrit-filter');
        $passeeresp = $request->request->get('passee-filter');

        //initialiser les variables
        $datedeb=null;
        $datefin=null;
        $nom=null;
        $organisateur=null;
        $passee=null;

        //preparation des filtres a mettre en place sur la requete
        //site
        if($siteresp!=""|| $siteresp!=null ){
        $site=$repoSite->findBy(array('nom' => $siteresp));}
        //organisateur
        if($organisateurresp==true){
            $organisateur=$this->getUser();}
        //date de debut
        if($datedebresp!=""){
            $datedeb=$datedebresp;
        }
        //date de fin
        if($datefinresp!=""){
            $datefin=$datefinresp;
        }
        //nom contient
        if($nameresp!=""){
            $nom=$nameresp."%";
        }
        //sorties passees
        if($passeeresp==true){
            $passee = date_create()->format('Y-m-d');
        }
        $sortiesFiltrees=$repo->sortieFiltree($site,$nom,$datedeb,$datefin,$organisateur,$passee);
        //recuperation des sorties pre-filtrees sur les criteres(site,organisateur,date de debut,date de fin,nom contient,sorties passees)
        $sorties=$sortiesFiltrees;

        //l'utilisateur est inscrit
        if($inscritresp && !$noninscritresp){
            $inscrit=$this->getUser();
            $sortiesinscrit=$inscrit->getSortiesInscriptions();
            //$listid=[];
            $sorties=[];
            $sortiesFiltrees=(array)$sortiesFiltrees;
            foreach ($sortiesinscrit as $cle => $valeur) {
                //array_push($listid,$valeur->getid());
                foreach ($sortiesFiltrees as $key => $valeursf) {
                    if($valeursf->getid()==$valeur->getid()){
                        array_push($sorties,$valeursf);
                        }
                }
            }
        }
        //l'utilisateur n'est pas inscrit
        if($noninscritresp && !$inscritresp) {
            $inscrit = $this->getUser();
            $sortiesinscrit = $inscrit->getSortiesInscriptions();
            $listidin = [];
            $sorties = [];
            $listidf = [];

            foreach ($sortiesinscrit as $cle => $valeur) {
                array_push($listidin, $valeur->getid());
            }
            foreach ($sortiesFiltrees as $cle => $valeur) {
                array_push($listidf, $valeur->getid());
            }

            foreach ($sortiesFiltrees as $key => $s) {
                $id = $s->getid();
                if (in_array($id, $listidin)) {
                } else {
                    array_push($sorties, $s);
                }
            }
        }



        return $this->render('main/index.html.twig', [
           'sorties'=>$sorties,
            'user'=>$user,
            'sites'=>$sites,

        ]);}


    /**
     * @Route("/sortie/{id}", name="detail_sortie")
     * @param Sortie $sortie
     * @return Response
     */
    public function afficherSortie(Sortie $sortie): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('sortie/detail_sortie.html.twig', [
            's' => $sortie,
            'user'=>$user,
        ]);
    }

}

