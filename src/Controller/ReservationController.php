<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\ConferenceRoomRepository;
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

    public function __construct(ReservationRepository $reservationRepository, ConferenceRoomRepository $conferenceRoomRepository)
    {
        $this->reservationRepository = $reservationRepository;
        $this->conferenceRoomRepository = $conferenceRoomRepository;
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

        $startTime = new \DateTime($data['startTime']);
        $endTime = new \DateTime($data['endTime']);
        if (!$startTime || !$endTime) {
            return $this->json(['message' => 'Invalid date format.'], Response::HTTP_BAD_REQUEST);
        }

        $reservedRoomId = $data['conferenceRoomId'] ?? null;
        $reservedBy = $data['reservedBy'] ?? null;

        $reservation = new Reservation();
        $reservation->setStartTime($startTime);
        $reservation->setEndTime($endTime);
        $reservation->setConferenceRoomId($reservedRoomId);
        $reservation->setReservedBy($reservedBy);

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $existingReservations = $this->reservationRepository->findByRoomAndTime($roomId, $startTime, $endTime);

        if ($existingReservations) {
            $conflictingReservations = [];
            // Zbieramy wszystkie kolidujące rezerwacje
            foreach ($existingReservations as $reservation) {
                $conflictingReservations[] = [
                    'reservedBy' => $reservation->getReservedBy(),
                    'startTime' => $reservation->getStartTime()->format('Y-m-d H:i:s'),
                    'endTime' => $reservation->getEndTime()->format('Y-m-d H:i:s'),
                ];
            }
            // Zwracamy wszystkie kolidujące rezerwacje w odpowiedzi
            return $this->json([
                'message' => 'The room is already reserved during this time.',
                'existingReservations' => $conflictingReservations,
            ], Response::HTTP_CONFLICT);
        }

        $this->reservationRepository->save($reservation);

        return $this->json(['message' => 'Reservation created successfully']);
    }
}
