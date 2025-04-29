<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Repository\StaffRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/staff', name: 'staff_routes')]
class StaffController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private StaffRepository $staffRepository;

    public function __construct(EntityManagerInterface $entityManager, StaffRepository $staffRepository)
    {
        $this->entityManager = $entityManager;
        $this->staffRepository = $staffRepository;
    }

    #[Route('/', name: 'get_staff', methods: ['GET'])]
    public function getStaff(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->staffRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get_staff_by_id', methods: ['GET'])]
    public function getStaffById(int $id): JsonResponse
    {
        $staff = $this->staffRepository->find($id);

        if (!$staff) {
            return $this->json(['message' => 'Staff not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($staff);
    }

    #[Route('/', name: 'create_staff', methods: ['POST'])]
    public function createStaff(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['role']) || empty($data['name']) || empty($data['phone'])) {
            return $this->json(['message' => 'Role, name, and phone are required.'], Response::HTTP_BAD_REQUEST);
        }

        $staff = new Staff();
        $staff->setRole($data['role']);
        $staff->setName($data['name']);
        $staff->setPhone($data['phone']);

        $this->entityManager->persist($staff);
        $this->entityManager->flush();

        return $this->json($staff, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_staff', methods: ['PATCH'])]
    public function updateStaff(Request $request, int $id): JsonResponse
    {
        $staff = $this->staffRepository->find($id);

        if (!$staff) {
            return $this->json(['message' => 'Staff not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['role'])) {
            $staff->setRole($data['role']);
        }

        if (isset($data['name'])) {
            $staff->setName($data['name']);
        }

        if (isset($data['phone'])) {
            $staff->setPhone($data['phone']);
        }

        $this->entityManager->flush();

        return $this->json($staff);
    }

    #[Route('/{id}', name: 'delete_staff', methods: ['DELETE'])]
    public function deleteStaff(int $id): JsonResponse
    {
        $staff = $this->staffRepository->find($id);

        if (!$staff) {
            return $this->json(['message' => 'Staff not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($staff);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
