<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModificationProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function editProfil(Request $request, Participant $participant): Response
    {
        $formProfil = $this->createForm(ModificationProfilType::class, $participant);
        $formProfil->handleRequest($request);
        if ($formProfil->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->render('main/afficherprofil.html.twig', [
                'participant' => $participant,
                'success_ajout' => 'null']);
        }
        return $this->render('main/modificationprofil.html.twig', [
            'formProfil' => $formProfil->createView(),
            'success_ajout' => 'null',
        ]);
    }

    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function profil(Participant $participant): Response
    {

        return $this->render('main/afficherprofil.html.twig',
            ['participant' => $participant
            ]);
    }
}
