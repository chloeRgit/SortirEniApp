<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Entity\Ville;
use App\Form\CreationLieuType;
use App\Form\CreationSortieType;
use App\Form\CreationVilleType;
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
     * @Route("/edit_sortie/{id}", name="edit_sortie")
     */
    public function editSortie(Request $request, Sortie $sortie,VilleRepository $villeRepository,LieuRepository $lieuRepository): Response
    {
        $villeRepo = $villeRepository->findAll();
        $user = $this->getUser();
        $lieux=$lieuRepository->findBy(['ville'=>$sortie->getLieu()->getVille()]);
        //dd($lieux);

        return $this->render('main/modifiersortie.html.twig', [
            's' => $sortie,
            'ville'=>$villeRepo,
            'user'=>$user,
            'lieux'=>$lieux,
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
            'user' => $organisteur,
            'lieux' => $lieuCat,
            'ville' => $villeRepo,
            'rue' => $rue,
            'cp' => $cp,
            'siteOrganisateur' => $organisteur->getSite()->getNom(),
            'formSortie' => $formSortie->createView(),
        ]);
    }

    /**
     * @Route("create_lieu", name="app_lieu")
     */
    public function creationLieu(Request $request, LieuRepository $lieuRepository, VilleRepository $villeRepository): Response
    {
        $lieu = new Lieu();
        $rue = null;
        $cp = null;
        $latitude = null;
        $longitude = null;

        $villeRepo = $villeRepository->findAll();

        $user = $this->getUser();

        $formLieu = $this->createForm(CreationLieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if (isset($_POST['action'])) {

            if ($request->request->get('nom-lieu') != null){
                $lieu->setNom($request->request->get('nom-lieu')) ;
            }

            if ($request->request->get('rue-lieu-sortie') != null){
                $lieu->setRue($request->request->get('rue-lieu-sortie')) ;
            }

            if ($request->request->get('cp-ville-select') != null){
                $ville = $villeRepository->findOneBy(['id'=>$request->request->get('cp-ville-select')]);
                    $lieu->setVille($ville);
            }

            if ($request->request->get('lat-lieu-sortie') != null){
                $lieu->setLatitude($request->request->get('lat-lieu-sortie')) ;
            }

            if ($request->request->get('long-lieu-sortie') != null){
                $lieu->setLongitude($request->request->get('long-lieu-sortie')) ;
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->render('main/creationsortie.html.twig', [
                'lieu' => $lieu,
                'ville' => $villeRepo,
                'user' => $user,
                'rue' => $rue,
                'cp' => $cp,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'siteOrganisateur' => $user->getSite()->getNom(),
                'formLieu' => $formLieu->createView(),
            ]);
        } else {

        }

        return $this->render('main/creationlieu.html.twig', [
            'lieu' => $lieu,
            'ville' => $villeRepo,
            'user' => $user,
            'rue' => $rue,
            'cp' => $cp,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'siteOrganisateur' => $user->getSite()->getNom(),
            'formLieu' => $formLieu->createView(),
        ]);
    }

    /**
     * @Route("create_ville", name="app_ville")
     */
    public function creationVille(Request $request, VilleRepository $villeRepository): Response
    {
        $ville = new Ville();
        $cp = null;

        $user = $this->getUser();

        $formVille = $this->createForm(CreationVilleType::class, $ville);
        $formVille->handleRequest($request);

        if (isset($_POST['action'])) {

            if ($request->request->get('ville-lieu') != null){
                $ville->setNom($request->request->get('ville-lieu')) ;
            }
            if ($request->request->get('cp-ville-lieu') != null){
                $ville->setCodePostal($request->request->get('cp-ville-lieu')) ;
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->render('main/creationlieu.html.twig', [
                'ville' => $ville,
                'cp' => $cp,
                'user' => $user,
                'siteOrganisateur' => $user->getSite()->getNom(),
                'formVille' => $formVille->createView(),
            ]);
        } else {

        }

        return $this->render('main/creationville.html.twig', [
            'ville' => $ville,
            'cp' => $cp,
            'user' => $user,
            'siteOrganisateur' => $user->getSite()->getNom(),
            'formVille' => $formVille->createView(),
        ]);
    }


    /**
     * @Route("/api/select_lieu/{id}", name="api_ville" ,methods={"GET"})
     */
    public function selectLieu(LieuRepository $lieuRepo, VilleRepository $villeRepository, $id): Response
    {
        // Je r??cup??re un table de lieu qui correspond ?? la ville
        $ville = $villeRepository->findOneBy(
            [
                "id" => $id
            ]
        );

        // Je fais un select dans la BDD
        $lieux = $lieuRepo->LieuFiltre($ville);

        // Je r??cup??re uniquement les donn??es dont j'ai besoin (ID et Nom) dans un tableau
        $tabLieux = array();
        $compteur=0;
        foreach ($lieux as $l){
            $tabLieux[$compteur]["id"] = $l->getId();
            $tabLieux[$compteur]["nom"] = $l->getNom();
            $compteur++;
        }

        // Je retourne mon tableau
        return $this->json($tabLieux);
    }

    /**
     * @Route("/api/info_lieu/{id}", name="api_lieux" ,methods={"GET"})
     */
    public function infoLieu(LieuRepository $lieuRepo, VilleRepository $repo, $id): Response
    {
        // Recherche du lieu en fonction de l'ID
        $lieu = $lieuRepo->findOneBy(
            [
                "id" => $id
            ]
        );

        // Je contourne le circular r??f??rence
        // en cr??ant un tableau avec uniquement les informations dont j'ai besoin
        $tabLieu = array();
        $tabLieu[0] = $lieu->getRue();
        $tabLieu[1] = $lieu->getVille()->getCodePostal();
        $tabLieu[2] = $lieu->getLatitude();
        $tabLieu[3] = $lieu->getLongitude();

        // Je retourne le tableau
        return $this->json($tabLieu);
    }
    /**
     * @Route("/inscrire/{id}", name="inscrire")
     * @param SortieRepository $repo
     * @return Response
     */
    public function inscrire(Sortie $sortie,EntityManagerInterface $em): Response
    {   $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user=$this->getUser();
        $sortie->addInscription($user);
        $em->flush();
        return $this->redirectToRoute('main');
    }
    /**
     * @Route("/desister/{id}", name="desister")
     * @param SortieRepository $repo
     * @return Response
     */
    public function desister(Sortie $sortie,EntityManagerInterface $em): Response
    {   $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user=$this->getUser();
        $sortie->removeInscription($user);
        $em->flush();
        return $this->redirectToRoute('main');
    }
    /**
     * @Route("/sortie/{id}", name="sortie")
     */
    public function afficherSortie(Sortie $sortie,SortieRepository $sortieRepository, LieuRepository $lieuRepository, SiteRepository $siteRepository, VilleRepository $villeRepository,EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('main/affichesortie.html.twig', [
            's' => $sortie,
            'user'=>$user,
         ]);
    }
    /**
     * @Route("/publier/{id}", name="publier_sortie")
     */
    public function publierSortie(Sortie $sortie,EntityManagerInterface $em, EtatRepository $repoEtat): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $etatpublier=$repoEtat->findOneBy(['libelle'=>'Ouverte']);
        $sortie->setEtat($etatpublier);
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('main');

    }
    /**
     * @Route("/annuler/{id}", name="annuler_sortie")
     */
    public function annulerSortie(Sortie $sortie,EntityManagerInterface $em, EtatRepository $repoEtat, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $etatannuler=$repoEtat->findOneBy(['libelle'=>'Annul??e']);
        $user = $this->getUser();
        if (isset($_POST['annulation'])) {
            //dd($request);
            if ($request->request->get('motif_annulation') != null){
                $sortie->setMotifAnnulation($request->request->get('motif_annulation')) ;
            }
            $sortie->setEtat($etatannuler);
            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute('main');
        }

        return $this->render('main/annulersortie.html.twig', [
            's' => $sortie,
            'user'=>$user,
        ]);

    }

}
