<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, SiteRepository $repo): Response
    {
        $participant=null;
        $user = new Participant();
        $sites = $repo->findAll();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if (isset($_POST['action'])) {
            #Todo : Pseudo
            if ($request->request->get('registration_form_pseudo') != null){
                $user->setPseudo($request->request->get('registration_form_pseudo')) ;
            }
            #Todo : Nom
            if ($request->request->get('registration_form_nom') != null){
                $user->setNom($request->request->get('registration_form_nom')) ;
            }
            #Todo : Prénom
            if ($request->request->get('registration_form_prenom') != null){
                $user->setPrenom($request->request->get('registration_form_prenom')) ;
            }
            #Todo : Email
            if ($request->request->get('registration_form_email') != null){
                $user->setEmail($request->request->get('registration_form_email')) ;
            }
            #Todo : Telephone
            if ($request->request->get('registration_form_telephone') != null){
                $user->setTelephone($request->request->get('registration_form_telephone')) ;
            }
            // encode the plain password
            $pass = $request->request->get('registration_form_Password');
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $pass
                )
            );
            #Todo : Site
            $idSite = $request->request->get('registration_form_site');
            $site = $repo->findOneBy([
                "id" => $idSite
            ]);

            if ($request->request->get('registration_form_site') != null){
                $user->setSite($site) ;
            }
            $user->setRoles(["ROLE_ADMIN"]);
            $user->setActif(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'user' => $participant,
            'sites' => $sites,
            'registrationForm' => $form->createView(),
        ]);
    }
}
