<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(SortieRepository $repo ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sorties=$repo->findAll();
        $user=$this->getUser();



        return $this->render('main/index.html.twig', [
            'sorties'=>$sorties,
            'user'=>$user,
            'controller_name' => 'MainController',
        ]);
    }
}
