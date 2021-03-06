<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\Participant;
use App\Form\ModificationProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="participant")
     */
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    /**
     * @Route("/edit_profil/{id}", name="edit_profil")
     */
    public function editProfil(Request $request, Participant $participant, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $error = null;
        $user = $this->getUser()->getUserIdentifier();
        $idProfil = $participant->getUserIdentifier();
        $autorisation = true;
        if ($user != $idProfil){
            return $this->redirectToRoute('profil',['id'=> $participant->getID()]);
        }
        $site = $participant->getSite()->getNom();
        $mdp_user = $participant->getPassword();
        $hash = $request->request->get('mdp');
        $mdp_c= $request->request->get('mdp_c');

        $formProfil = $this->createForm(ModificationProfilType::class, $participant);
        $formProfil->handleRequest($request);
        if (isset($_POST['action'])) {
            if (password_verify($mdp_c, $mdp_user)) {
            } else {
                if($hash == $mdp_c){
                    $participant->setPassword(
                        $passwordEncoder->encodePassword(
                            $participant,
                            $mdp_c
                        )
                    );
                }
                else{
                    $error = "Votre mot de passe est diff??rent de votre confirmation";
                }
            }
            if ($request->request->get('tel') != null){
                $participant->setTelephone($request->request->get('tel'));
            }

            if ($request->request->get('mail') != null){
                $participant->setEmail($request->request->get('mail'));
            }
            if ($request->files->get('fileToUpload') != null){
                $avatar =$request->files->get('fileToUpload');

                    $fichier = md5(uniqid()).'.'.$avatar->guessExtension();
                    $avatar->move(
                        $this->getParameter('avatars_directory'),
                        $fichier);
                        $img = new Avatar();
                        $img->setName($fichier);
                        if ($participant->getAvatars()->first()){
                            $avatar1=$participant->getAvatars()->first();
                            //dd($avatar1);
                            $participant->removeAvatar($avatar1);
                        };
                        $participant->addAvatar($img);}

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return $this->render('main/modificationprofil.html.twig', [
            'formProfil' => $formProfil->createView(),
            'success_ajout' => 'null',
            'participant' => $participant,
            'user' => $participant,
            'autorisation' => $autorisation,
            'error' => $error,
            'site' => $site,
            'avatar'=>$participant->getAvatars()->first(),
        ]);
    }

    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function profil(Participant $participant): Response
    {

        $user = $this->getUser()->getUserIdentifier();
        $idProfil = $participant->getUserIdentifier();
        $autorisation = true;
        if ($user != $idProfil){
            $autorisation = false;
        }

        return $this->render('main/afficherprofil.html.twig',
            [
                'user' => $participant,
                'autorisation' => $autorisation,
                'participant' => $participant,
                'avatar'=>$participant->getAvatars()->first(),
            ]);
    }
}
