<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(VehicleRepository $vehicleRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $vehicles = $vehicleRepository->findAll();

        $session = new Session();
        $session->set('checkedIn', false);


        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'vehicles' => $vehicles
        ]);
    }
}
