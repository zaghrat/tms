<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/administration')]
class AdministrationController extends AbstractController
{
    #[Route('/list-users', name: 'app_administration_list_users')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('administration/list-users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/time-entries-per-user/{id}', name: 'app_administration_time_entries_per_user')]
    #[ParamConverter('user', User::class)]
    public function timeEntriesPerUser(User $user): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('administration/time-entries-per-user.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/list-vehicles', name: 'app_administration_list_vehicles')]
    public function listVehicles(VehicleRepository $vehicleRepository): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('administration/list-vehicles.html.twig', [
            'vehicles' => $vehicleRepository->findAll(),
        ]);
    }
}
