<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreationSortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use App\Services\StateLabel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/create_sortie", name="create_sortie")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param LieuRepository $lieuRepository
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function creationSortie(EntityManagerInterface $entityManager,
                                   Request $request,
                                   VilleRepository $villeRepository,
                                   LieuRepository $lieuRepository,
                                   EtatRepository $etatRepository): Response
    {
        // initialisation des variables
        $sortie = new Sortie();
        $rue = null;
        $cp = null;
        $lieuChoisi = null;

        // On récupère le nom du campus
        $siteOrganisateur = $this->getUser()->getSite()->getNom();

        $lieuCat = $lieuRepository->findAll();
        $villeRepo = $villeRepository->findAll();

        // on récupère l'organisateur
        $organisteur = $this->getUser();

        // on crée le formulaire
        $creationSortieForm = $this->createForm(CreationSortieType::class, $sortie);
        $creationSortieForm->handleRequest($request);

        // si on enregistre ou publie la sortie (et que la saisie est conforme), on hydrate l'objet correspondant
//        if ($creationSortieForm->isSubmitted() && $creationSortieForm->isValid() ) {
//
//            $sortie->setOrganisateur($organisteur);
//            $sortie->setSite($this->getUser()->getSite());
//
//            // pour le lieu choisi (hors formulaire)
//            if ($request->request->get('lieu-select') != null){
//                $idLieu = $request->request->get('lieu-select');
//                $lieuChoisi = $lieuRepository->findOneBy([
//                    "id" => $idLieu
//                ]);
//                $sortie->setLieu($lieuChoisi);
//            }
//
//            // si on publie la sortie, son état passe à "publiée" et un message nous en averti
//            if ($request->get('publish')) {
//                $etat = $etatRepository->findOneBy(['wording' => StateLabel::STATE_OPEN]);
//
//                // On ajoute le message flash correspondant
//                $this->addFlash("link", "Votre sortie a été créée et publiée");
//
//            // si on ne fait qu'enregistrer la sortie, son état passe à "créée" et un message nous en averti
//            } else {
//                $etat = $etatRepository->findOneBy(['wording' => StateLabel::STATE_CREATED]);
//
//                // On ajoute le message flash correspondant
//                $this->addFlash("link", "Votre sortie a été créée. Il faut penser à la publier");
//            }
//
//            $sortie->setEtat($etat);
//
//            // on confie l'objet $sortie au gestionnaire d'entités, l'objet n'est pas encore enregistré en base de données
//            $entityManager->persist($sortie);
//
//            // on exécute la requête qui va ajouter l'objet $sortie en base de données
//            $entityManager->flush();
//
//            // on redirige vers la page principale
//            return $this->redirectToRoute('main');
//        }
//
//        return $this->render('sortie/create_sortie.html.twig', [
//            'creationSortieForm' => $creationSortieForm->createView(),
//            'sortie' => $sortie,
//            'organisateur' => $organisteur,
//            'user' => $organisteur,
//            'siteOrganisateur' => $siteOrganisateur,
//            'ville' => $villeRepo,
//            'lieux' => $lieuCat,
//            'lieu' => $lieuChoisi,
//            'rue' => $rue,
//            'cp' => $cp,
//        ]);
//    }

        // Si les données du formulaire ne sont pas nulles, on les affecte aux objets correspondants
        if (isset($_POST['action'])) {

            if ($request->request->get('titre-sortie') != null) {
                $sortie->setNom($request->request->get('titre-sortie'));
            }

            if ($request->request->get('date-h-sortie') != null) {
                $timeSortie = new \DateTime($request->request->get('date-h-sortie'));
                $timeSortie->format('Y-m-d H:i:s');
                $sortie->setDateHeureDebut($timeSortie);
            }

            if ($request->request->get('date-l-sortie') != null) {
                $timeInscription = new \DateTime($request->request->get('date-l-sortie'));
                $timeInscription->format('Y-m-d H:i:s');
                $sortie->setDateLimiteInscription($timeInscription);
            }

            if ($request->request->get('nbParticipant-sortie') != null) {
                $sortie->setNbInscriptionsMax($request->request->get('nbParticipant-sortie'));
            }

            if ($request->request->get('duree-sortie') != null) {
                $sortie->setDuree($request->request->get('duree-sortie'));
            }

            $sortie->setInfosSortie($request->request->get('desc-sortie'));

            if ($request->request->get('lieu-select') != null) {
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

            // on récupère le gestionnaire d'entités qui va nous permettre d'enregistrer l'événement
            $entityManager = $this->getDoctrine()->getManager();

            // on confie l'objet $sortie au gestionnaire d'entités, l'objet n'est pas encore enregistré en base de données
            $entityManager->persist($sortie);

            // on exécute la requête qui va ajouter l'objet $sortie en base de données
            $entityManager->flush();

            // on redirige vers la page principale
            return $this->redirectToRoute('main');
        } else {
            // TO DO : gestion des erreurs
        }

        return $this->render('sortie/create_sortie.html.twig', [
            'sortie' => $sortie,
            'organisateur' => $organisteur,
            'user' => $organisteur,
            'lieux' => $lieuCat,
            'ville' => $villeRepo,
            'rue' => $rue,
            'cp' => $cp,
            'siteOrganisateur' => $organisteur->getSite()->getNom(),
            'creationSortieForm' => $creationSortieForm->createView(),
        ]);
    }

    /**
     * @Route("/api/select_lieu/{id}", name="api_ville" ,methods={"GET"})
     */
    public function selectLieu(LieuRepository $lieuRepo, VilleRepository $villeRepository, $id): Response
    {
        // Je récupère une table de lieu qui correspond à la ville choisie
        $ville = $villeRepository->findOneBy(
            [
                "id" => $id
            ]
        );

        // Je fais un select dans la BDD
        $lieux = $lieuRepo->LieuFiltre($ville);

        // Je récupère uniquement les données dont j'ai besoin (ID et Nom) dans un tableau
        $tabLieux = array();
        $compteur = 0;
        foreach ($lieux as $l) {
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
        // on va chercher le lieu en fonction de son ID
        $lieu = $lieuRepo->findOneBy(
            [
                "id" => $id
            ]
        );

        // on contourne le circular référence en créant un tableau avec uniquement les informations dont on a besoin
        $tabLieu = array();
        $tabLieu[0] = $lieu->getRue();
        $tabLieu[1] = $lieu->getVille()->getCodePostal();
//        $tabLieu[2] = $lieu->getLatitude();
//        $tabLieu[3] = $lieu->getLongitude();

        // on retourne le tableau
        return $this->json($tabLieu);
    }
}
