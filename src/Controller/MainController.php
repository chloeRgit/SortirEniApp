<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
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

        $sorties=$repo->findAll();
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

        $datedeb=null;
        $datefin=null;
        $nom=null;
        $organisateur=null;
        $passee=null;

        if($siteresp!=""|| $siteresp!=null ){
        $site=$repoSite->findBy(array('nom' => $siteresp));}

        if($organisateurresp==true){
            $organisateur=$this->getUser();}


        if($datedebresp!=""){
            $datedeb=$datedebresp;
        }
        if($datefinresp!=""){
            $datefin=$datefinresp;
        }
        if($nameresp!=""){
            $nom=$nameresp."%";
        }
        if($passeeresp==true){
            $passee = date_create()->format('Y-m-d');
        }
        $sortiesFiltrees=$repo->sortieFiltree($site,$nom,$datedeb,$datefin,$organisateur,$passee);

        if($inscritresp==true){
            $inscrit=$this->getUser();
            $sortiesinscrit=$inscrit->getSortiesInscriptions();
            $listid=[];
            $sorties=[];
            $sortiesFiltrees=(array)$sortiesFiltrees;
            foreach ($sortiesinscrit as $cle => $valeur) {
                array_push($listid,$valeur->getid());
                foreach ($sortiesFiltrees as $key => $valeursf) {
                    if($valeursf->getid()==$valeur->getid()){
                        array_push($sorties,$valeursf);
                        }
                }
            }
            dd($listid,$sorties);

                //echo $cle.' - '.$valeur->getid().'<br />'."\n";
          //  for ($i = 0; $i <= $sortiesinscrit.lenght(); $i++){

           // for (si in $sortiesinscrit){



            //if ($sorties = array_intersect($sortiesFiltrees, array_filter($sortiesinscrit))) {
                //^^^^^^^^^^^^ See here
              //  $intersection [] = $sorties;
            //}
           //$sorties = array_intersect($sortiesinscrit, $sortiesFiltrees);
           // $sorties=array_uintersect_assoc($sortiesinscrit,$sortiesFiltrees);
       // dd($intersection);

            //dd($sorties);
           // foreach ($sortiesFiltrees){}
        }
        if($noninscritresp==true){
            $noninscrit=$this->getUser();
        }




      // dd($sortiesFiltrees);
       // dd($siteresp,$name , $datedeb,$datefin,$organisateur,$inscrit,$noninscrit,$passee);




        return $this->render('main/index.html.twig', [
           'sorties'=>$sorties,
            'user'=>$user,
            'sites'=>$sites,

        ]);}




}

