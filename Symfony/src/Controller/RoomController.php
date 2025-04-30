<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/room', name: 'room_routes')]
class RoomController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private RoomRepository $roomRepository;

    public function __construct(EntityManagerInterface $entityManager, RoomRepository $roomRepository)
    {
        $this->entityManager = $entityManager;
        $this->roomRepository = $roomRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_rooms', methods: ['GET'])]
    public function getRooms(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->roomRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_room', methods: ['GET'])]
    public function getRoom(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);

        if (!$room) {
            return $this->json(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($room);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_room', methods: ['POST'])]
    public function createRoom(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['number']) || empty($data['type']) || empty($data['capacity']) || empty($data['price'])) {
            return $this->json(['message' => 'Number, type, capacity, and price are required.'], Response::HTTP_BAD_REQUEST);
        }

        $room = new Room();
        $room->setNumber($data['number']);
        $room->setType($data['type']);
        $room->setCapacity($data['capacity']);
        $room->setPrice($data['price']);

        $this->entityManager->persist($room);
        $this->entityManager->flush();

        return $this->json($room, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_room', methods: ['PATCH'])]
    public function updateRoom(Request $request, int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);

        if (!$room) {
            return $this->json(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['number'])) {
            $room->setNumber($data['number']);
        }

        if (isset($data['type'])) {
            $room->setType($data['type']);
        }

        if (isset($data['capacity'])) {
            $room->setCapacity($data['capacity']);
        }

        if (isset($data['price'])) {
            $room->setPrice($data['price']);
        }

        $this->entityManager->flush();

        return $this->json($room);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_room', methods: ['DELETE'])]
    public function deleteRoom(int $id): JsonResponse
    {
        $room = $this->roomRepository->find($id);

        if (!$room) {
            return $this->json(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($room);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
