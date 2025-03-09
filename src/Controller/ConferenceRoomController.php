<?php

namespace App\Controller;

use App\Entity\ConferenceRoom;
use App\Repository\ConferenceRoomRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;


final class ConferenceRoomController extends AbstractController
{
    private $conferenceRoomRepository;

    public function __construct(ConferenceRoomRepository $conferenceRoomRepository)
    {
        $this->conferenceRoomRepository = $conferenceRoomRepository;
    }

    #[Route('/api/conference-rooms', name: 'create_conference_room', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->toArray();
        $conferenceRoom = new ConferenceRoom();
        $conferenceRoom->setName($data['name'] ?? null);
        $conferenceRoom->setCapacity($data['capacity'] ?? null);

        $errors = $validator->validate($conferenceRoom);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $existingRoom = $this->conferenceRoomRepository->findOneByName($conferenceRoom->getName());
        if ($existingRoom) {
            return $this->json(["message" => "Sala już istnieje"], Response::HTTP_BAD_REQUEST);
        }

        $this->conferenceRoomRepository->save($conferenceRoom);

        return $this->json(
            ['message' => 'Conference Room created successfully', 'id' => $conferenceRoom->getId()],
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/conference-rooms/{id}', name: 'edit_conference_room', methods: ['POST'])]
    public function edit(int $id, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $conferenceRoom = $this->conferenceRoomRepository->find($id);

        if (!$conferenceRoom) {
            return $this->json(['message' => 'Conference Room not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->toArray();
        $conferenceRoom->setName($data['name'] ?? null);
        $conferenceRoom->setCapacity($data['capacity'] ?? null);

        $errors = $validator->validate($conferenceRoom);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->conferenceRoomRepository->save($conferenceRoom);

        return $this->json(['message' => 'Conference Room updated successfully']);
    }

    #[Route('/api/conference-rooms/{id}', name: 'delete_conference_room', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $conferenceRoom = $this->conferenceRoomRepository->find($id);

        if (!$conferenceRoom) {
            return $this->json(['message' => 'Conference Room not found'], Response::HTTP_NOT_FOUND);
        }

        $this->conferenceRoomRepository->delete($conferenceRoom);

        return $this->json(['message' => 'Conference Room deleted successfully']);
    }
}
