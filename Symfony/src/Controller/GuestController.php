<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Repository\GuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/guest', name: 'guest_routes')]
class GuestController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private GuestRepository $guestRepository;

    public function __construct(EntityManagerInterface $entityManager, GuestRepository $guestRepository)
    {
        $this->entityManager = $entityManager;
        $this->guestRepository = $guestRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_guests', methods: ['GET'])]
    public function getGuests(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->guestRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_guest', methods: ['GET'])]
    public function getGuest(int $id): JsonResponse
    {
        $guest = $this->guestRepository->find($id);

        if (!$guest) {
            return $this->json(['message' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($guest);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_guest', methods: ['POST'])]
    public function createGuest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['documentNumber']) || empty($data['phone'])) {
            return $this->json(['message' => 'Document number and phone are required.'], Response::HTTP_BAD_REQUEST);
        }

        $guest = new Guest();
        $guest->setDocumentNumber($data['documentNumber']);
        $guest->setPhone($data['phone']);

        $this->entityManager->persist($guest);
        $this->entityManager->flush();

        return $this->json($guest, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_guest', methods: ['PATCH'])]
    public function updateGuest(Request $request, int $id): JsonResponse
    {
        $guest = $this->guestRepository->find($id);

        if (!$guest) {
            return $this->json(['message' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['documentNumber'])) {
            $guest->setDocumentNumber($data['documentNumber']);
        }

        if (isset($data['phone'])) {
            $guest->setPhone($data['phone']);
        }

        $this->entityManager->flush();

        return $this->json($guest);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_guest', methods: ['DELETE'])]
    public function deleteGuest(int $id): JsonResponse
    {
        $guest = $this->guestRepository->find($id);

        if (!$guest) {
            return $this->json(['message' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($guest);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
