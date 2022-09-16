<?php

namespace App\Controller;

use App\Entity\TimeTrackingEntry;
use App\Entity\User;
use App\Repository\TimeTrackingEntryRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(
        VehicleRepository $vehicleRepository,
        TimeTrackingEntryRepository $timeTrackingEntryRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $vehicles = $vehicleRepository->findAll();

        $session = new Session();
        if ($session->get('checkedIn') === null)
        {
            $lastEntry = $timeTrackingEntryRepository->findOneBy([
               'user' =>$this->getUser(),
               'isCheckedOut' => false,
                'type' => TimeTrackingEntry::CHECK_IN
            ], ['created' => 'DESC']);

            $pause = $timeTrackingEntryRepository->findOneBy([
                'user' =>$this->getUser(),
                'isCheckedOut' => false,
                'type' => TimeTrackingEntry::START_PAUSE
            ], ['created' => 'DESC']);

            $session->set('checkedIn', !is_null($lastEntry));
            $session->set('vehicle', $lastEntry?->getVehicle()->getId());
            $session->set('in_pause', !is_null($pause));
        }


        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'vehicles' => $vehicles
        ]);
    }

    #[NoReturn]
    #[Route('/check-in', name: 'app_checkIn', methods: "post")]
    public function checkIn(
        Request $request,
        VehicleRepository $vehicleRepository,
        TimeTrackingEntryRepository $timeTrackingEntryRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $vehicle_id = $request->request->get('vehicle');
        $vehicle = $vehicleRepository->find($vehicle_id);
        $user = $this->getUser();

        $timeTrackingEntry = new TimeTrackingEntry();
        $timeTrackingEntry
            ->setVehicle($vehicle)
            ->setUser($user)
            ->setType(TimeTrackingEntry::CHECK_IN)
            ->setCreated(new \DateTimeImmutable())
            ->setUpdated(new \DateTime())
        ;

        $timeTrackingEntryRepository->add($timeTrackingEntry, true);

        $session = new Session();
        $session->set('checkedIn', true);
        $session->set('vehicle', $vehicle->getId());
        $session->set('in_pause', false);

        return $this->redirectToRoute("app_index");
    }

    #[Route('/start-pause', name: 'app_start_pause', methods: "get")]
    public function startPause(
        VehicleRepository $vehicleRepository,
        TimeTrackingEntryRepository $timeTrackingEntryRepository
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = new Session();
        $vehicleId = $session->get('vehicle');

        $vehicle = $vehicleRepository->find($vehicleId);
        $user = $this->getUser();

        $timeTrackingEntry = new TimeTrackingEntry();
        $timeTrackingEntry
            ->setVehicle($vehicle)
            ->setUser($user)
            ->setType(TimeTrackingEntry::START_PAUSE)
            ->setCreated(new \DateTimeImmutable())
            ->setUpdated(new \DateTime())
        ;

        $timeTrackingEntryRepository->add($timeTrackingEntry, true);

        $session = new Session();
        $session->set('in_pause', true);

        return $this->redirectToRoute("app_index");
    }

    #[Route('/complete-pause', name: 'app_complete_pause', methods: "get")]
    public function completePause(
        VehicleRepository $vehicleRepository,
        TimeTrackingEntryRepository $timeTrackingEntryRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = new Session();
        $vehicleId = $session->get('vehicle');

        $vehicle = $vehicleRepository->find($vehicleId);
        $user = $this->getUser();


        $pause = $timeTrackingEntryRepository->findOneBy([
            'user' =>$this->getUser(),
            'isCheckedOut' => false,
            'type' => TimeTrackingEntry::START_PAUSE
        ], ['created' => 'DESC']);

        if (!$pause) {
            throw new \RuntimeException('No Pause to mark as completed!');
        }

        $pause
            ->setIsCheckedOut(true)
            ->setUpdated(new \DateTime())
        ;

        $timeTrackingEntry = new TimeTrackingEntry();
        $timeTrackingEntry
            ->setVehicle($vehicle)
            ->setUser($user)
            ->setType(TimeTrackingEntry::COMPLETE_PAUSE)
            ->setCreated(new \DateTimeImmutable())
            ->setUpdated(new \DateTime())
            ->setIsCheckedOut(true)
        ;


        $entityManager->persist($pause);
        $entityManager->persist($timeTrackingEntry);

        $entityManager->flush();

        $session = new Session();
        $session->set('in_pause', false);

        return $this->redirectToRoute("app_index");
    }

    #[Route('/check-out', name: 'app_checkOut', methods: "get")]
    public function checkOut(
        VehicleRepository $vehicleRepository,
        TimeTrackingEntryRepository $timeTrackingEntryRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = new Session();
        $vehicleId = $session->get('vehicle');

        $vehicle = $vehicleRepository->find($vehicleId);
        $user = $this->getUser();

        $lastEntry = $timeTrackingEntryRepository->findOneBy([
            'user' =>$this->getUser(),
            'isCheckedOut' => false,
            'type' => TimeTrackingEntry::CHECK_IN
        ], ['created' => 'DESC']);

        if (!$lastEntry) {
            throw new \RuntimeException('No Check In to mark as checked Out!');
        }

        $lastEntry
            ->setUpdated(new \DateTime())
            ->setIsCheckedOut(true)
        ;

        $timeTrackingEntry = new TimeTrackingEntry();
        $timeTrackingEntry
            ->setVehicle($vehicle)
            ->setUser($user)
            ->setType(TimeTrackingEntry::CHECK_OUT)
            ->setCreated(new \DateTimeImmutable())
            ->setUpdated(new \DateTime())
            ->setIsCheckedOut(true)
        ;


        $entityManager->persist($lastEntry);
        $entityManager->persist($timeTrackingEntry);

        $entityManager->flush();

        $session = new Session();
        $session->set('checkedIn', false);
        $session->set('vehicle', null);

        return $this->redirectToRoute("app_index");

    }
}
