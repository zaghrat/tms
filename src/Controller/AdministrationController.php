<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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

    #[Route('/user-details/{$id}', name: 'app_administration_user_details')]
    public function userDetails(User $user): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('administration/user-details.html.twig', [
            'user' => $user
        ]);
    }
}
