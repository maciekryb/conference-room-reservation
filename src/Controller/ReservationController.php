<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\ConferenceRoomRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends AbstractController
{
    private $reservationRepository;
    private $conferenceRoomRepository;
    private $notificationService;

    public function __construct(
        ReservationRepository $reservationRepository,
        ConferenceRoomRepository $conferenceRoomRepository,
        NotificationService  $notificationService
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->conferenceRoomRepository = $conferenceRoomRepository;
        $this->notificationService = $notificationService;
    }

    #[Route('/api/reservations', name: 'create_reservation', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->toArray();

        $roomId = $data['conferenceRoomId'] ?? null;
        if (!$roomId) {
            return $this->json(['message' => 'Conference room id required'], Response::HTTP_BAD_REQUEST);
        }
        $conferenceRoom = $this->conferenceRoomRepository->find($roomId);
        if (!$conferenceRoom) {
            return $this->json(['message' => 'Conference room not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $startTime = new \DateTime($data['startTime'] ?? '');
            $endTime = new \DateTime($data['endTime'] ?? '');
        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid date format.'], Response::HTTP_BAD_REQUEST);
        }

        $reservation = new Reservation();
        $reservation->setStartTime($startTime);
        $reservation->setEndTime($endTime);
        $reservation->setConferenceRoomId($roomId);
        $reservation->setReservedBy($data['reservedBy'] ?? null);

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $conflictResponse = $this->checkForConflictingReservations($roomId, $startTime, $endTime);
        if ($conflictResponse) {
            return $conflictResponse;
        }

        $this->reservationRepository->save($reservation);
        $this->notificationService->sendReservationNotification($reservation, 'created');

        return $this->json(['message' => 'Reservation created and notification sent']);
    }

    private function checkForConflictingReservations(int $roomId, \DateTime $startTime, \DateTime $endTime): ?JsonResponse
    {
        $existingReservations = $this->reservationRepository->findByRoomAndTime($roomId, $startTime, $endTime);

        if (!$existingReservations) {
            return null;
        }

        $conflictingReservations = [];
        foreach ($existingReservations as $reservation) {
            $conflictingReservations[] = [
                'reservedBy' => $reservation->getReservedBy(),
                'startTime' => $reservation->getStartTime()->format('Y-m-d H:i:s'),
                'endTime' => $reservation->getEndTime()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json([
            'message' => 'The room is already reserved during this time.',
            'existingReservations' => $conflictingReservations,
        ], Response::HTTP_CONFLICT);
    }
}
