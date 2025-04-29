<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/service', name: 'service_routes')]
class ServiceController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ServiceRepository $serviceRepository;

    public function __construct(EntityManagerInterface $entityManager, ServiceRepository $serviceRepository)
    {
        $this->entityManager = $entityManager;
        $this->serviceRepository = $serviceRepository;
    }

    #[Route('/', name: 'get_services', methods: ['GET'])]
    public function getServices(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->serviceRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get_service', methods: ['GET'])]
    public function getService(int $id): JsonResponse
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return $this->json(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($service);
    }

    #[Route('/', name: 'create_service', methods: ['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['price'])) {
            return $this->json(['message' => 'Name and price are required.'], Response::HTTP_BAD_REQUEST);
        }

        $service = new Service();
        $service->setName($data['name']);
        $service->setPrice($data['price']);

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $this->json($service, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_service', methods: ['PATCH'])]
    public function updateService(Request $request, int $id): JsonResponse
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return $this->json(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $service->setName($data['name']);
        }

        if (isset($data['price'])) {
            $service->setPrice($data['price']);
        }

        $this->entityManager->flush();

        return $this->json($service);
    }

    #[Route('/{id}', name: 'delete_service', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        $service = $this->serviceRepository->find($id);

        if (!$service) {
            return $this->json(['message' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($service);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
