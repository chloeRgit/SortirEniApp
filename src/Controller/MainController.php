<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(SortieRepository $repo, SiteRepository $repoSite,EtatRepository $repoEtat): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        //recuperation des données
        $sorties=$repo->FindAllOrderByDateSortie();


        //$sorties=$repo->findBy(['dateHeureDebut' => 'ASC']);

        $user=$this->getUser();
        $sites=$repoSite->findAll();

        //update des états
        $etatouvert=$repoEtat->findOneBy(['libelle'=>'Ouverte']);
        $etatcloture=$repoEtat->findOneBy(['libelle'=>'Fermée']);
        $etatencours=$repoEtat->findOneBy(['libelle'=>'En cours']);
        $etatpasse=$repoEtat->findOneBy(['libelle'=>'Passée']);
        $now =new \DateTime();

        $em=$this->getDoctrine()->getManager();


        foreach ($sorties as $s ) {
           $dtdebh =$s->getDateHeureDebut();
           $time = new \DateTime($s->getDateHeureDebut()->format('Y-m-d H:i:s'));
           $time->add(new DateInterval('PT' . $s->getDuree() . 'M'));
           $dtfinh = $time->format('Y-m-d H:i');

            if(count($s->getInscription())>=$s->getNbInscriptionsMax() && $s->getEtat()->getId()==$etatouvert->getId()){
                $s->setEtat($etatcloture);
                $em->persist($s);
                $em->flush();
            }
            if(count($s->getInscription())<$s->getNbInscriptionsMax() && $s->getEtat()->getId()==$etatcloture->getId() && $s->getDateLimiteInscription()->format("Y-m-d H:i:s")<=$now->format("Y-m-d H:i:s")){
                $s->setEtat($etatouvert);
                $em->persist($s);
                $em->flush();
              }

            if($s->getEtat()->getId()==$etatouvert->getId() && ($s->getDateLimiteInscription()->format("Y-m-d H:i:s")<$now->format("Y-m-d H:i:s"))){

                $s->setEtat($etatcloture);
                $em->persist($s);
                $em->flush();
            }
           if(($s->getEtat()->getId()==$etatouvert->getId()|| $s->getEtat()->getId()==$etatcloture->getId())
                && ($dtdebh->format("Y-m-d H:i:s")<$now->format("Y-m-d H:i:s")
                    && $dtfinh>$now->format("Y-m-d H:i:s")) ){

                $s->setEtat($etatencours);
                $em->persist($s);
                $em->flush();
            }
            if(($s->getEtat()->getId()==$etatencours->getId()|| $s->getEtat()->getId()==$etatcloture->getId()||$s->getEtat()->getId()==$etatencours->getId()) && $dtfinh<$now->format("Y-m-d H:i:s")) {

                $s->setEtat($etatpasse);
                $em->persist($s);
                $em->flush();
            }



        }


        return $this->render('main/index.html.twig', [
            'sorties'=>$sorties,
            'user'=>$user,
            'sites'=>$sites,
            'controller_name' => 'MainController',
            'site'=>null,
            'name'=>null,
            'datedeb'=>null,
            'datefin'=>null,
            'organisateurfilter'=>null,
            'inscritfilter'=>null,
            'noninscritfilter'=>null,
            'passeefilter'=>null,
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

            $datefin=$datefinresp.'23:59:59';
        }
        //nom contient
        if($nameresp!=""){
            $nom=$nameresp."%";
        }
        //sorties passees
        if($passeeresp==true){
            $passee = date_create()->format('Y-m-d');
        } else{$passeeresp=null;}
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
            $inscritresp=true;
            $noninscritresp=null;
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
            $inscritresp=null;
            $noninscritresp=true;
        }



        return $this->render('main/index.html.twig', [
           'sorties'=>$sorties,
            'user'=>$user,
            'sites'=>$sites,
            'site'=>$siteresp,
            'name'=>$nameresp,
            'datedeb'=>$datedebresp,
            'datefin'=>$datefinresp,
            'organisateurfilter'=>$organisateurresp,
            'inscritfilter'=>$inscritresp,
            'noninscritfilter'=>$noninscritresp,
            'passeefilter'=>$passeeresp,

        ]);}




}

