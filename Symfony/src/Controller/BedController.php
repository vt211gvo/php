<?php

namespace App\Controller;

use App\Entity\Bed;
use App\Entity\Room;
use App\Repository\BedRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bed', name: 'bed_routes')]
class BedController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BedRepository $bedRepository;
    private RoomRepository $roomRepository;

    public function __construct(EntityManagerInterface $entityManager, BedRepository $bedRepository, RoomRepository $roomRepository)
    {
        $this->entityManager = $entityManager;
        $this->bedRepository = $bedRepository;
        $this->roomRepository = $roomRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_beds', methods: ['GET'])]
    public function getBeds(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->bedRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_bed', methods: ['GET'])]
    public function getBed(int $id): JsonResponse
    {
        $bed = $this->bedRepository->find($id);

        if (!$bed) {
            return $this->json(['message' => 'Bed not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($bed);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_bed', methods: ['POST'])]
    public function createBed(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['bedNumber'])) {
            return $this->json(['message' => 'Bed number is required.'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($data['roomId'])) {
            return $this->json(['message' => 'Room ID is required.'], Response::HTTP_BAD_REQUEST);
        }

        $room = $this->roomRepository->find($data['roomId']);
        if (!$room) {
            return $this->json(['message' => 'Room not found.'], Response::HTTP_NOT_FOUND);
        }

        $bed = new Bed();
        $bed->setBedNumber($data['bedNumber']);
        $bed->setRoom($room);

        $this->entityManager->persist($bed);
        $this->entityManager->flush();

        return $this->json($bed, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_bed', methods: ['PATCH'])]
    public function updateBed(Request $request, int $id): JsonResponse
    {
        $bed = $this->bedRepository->find($id);

        if (!$bed) {
            return $this->json(['message' => 'Bed not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['bedNumber'])) {
            $bed->setBedNumber($data['bedNumber']);
        }

        if (isset($data['roomId'])) {
            $room = $this->roomRepository->find($data['roomId']);
            if (!$room) {
                return $this->json(['message' => 'Room not found.'], Response::HTTP_NOT_FOUND);
            }
            $bed->setRoom($room);
        }

        $this->entityManager->flush();

        return $this->json($bed);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_bed', methods: ['DELETE'])]
    public function deleteBed(int $id): JsonResponse
    {
        $bed = $this->bedRepository->find($id);

        if (!$bed) {
            return $this->json(['message' => 'Bed not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($bed);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
