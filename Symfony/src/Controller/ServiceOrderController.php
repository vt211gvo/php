<?php

namespace App\Controller;

use App\Entity\ServiceOrder;
use App\Repository\ServiceOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/service-order', name: 'service_order_routes')]
class ServiceOrderController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ServiceOrderRepository $serviceOrderRepository;

    public function __construct(EntityManagerInterface $entityManager, ServiceOrderRepository $serviceOrderRepository)
    {
        $this->entityManager = $entityManager;
        $this->serviceOrderRepository = $serviceOrderRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_service_orders', methods: ['GET'])]
    public function getServiceOrders(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->serviceOrderRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_service_order', methods: ['GET'])]
    public function getServiceOrder(int $id): JsonResponse
    {
        $serviceOrder = $this->serviceOrderRepository->find($id);

        if (!$serviceOrder) {
            return $this->json(['message' => 'Service order not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($serviceOrder);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_service_order', methods: ['POST'])]
    public function createServiceOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['guest']) || empty($data['service']) || empty($data['orderDate'])) {
            return $this->json(['message' => 'Guest, service, and order date are required.'], Response::HTTP_BAD_REQUEST);
        }

        $guest = $this->getDoctrine()->getRepository(Guest::class)->find($data['guest']);
        $service = $this->getDoctrine()->getRepository(Service::class)->find($data['service']);
        $orderDate = new \DateTime($data['orderDate']);

        if (!$guest || !$service) {
            return $this->json(['message' => 'Invalid guest or service.'], Response::HTTP_BAD_REQUEST);
        }

        $serviceOrder = new ServiceOrder();
        $serviceOrder->setGuest($guest);
        $serviceOrder->setService($service);
        $serviceOrder->setOrderDate($orderDate);

        $this->entityManager->persist($serviceOrder);
        $this->entityManager->flush();

        return $this->json($serviceOrder, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_service_order', methods: ['PATCH'])]
    public function updateServiceOrder(Request $request, int $id): JsonResponse
    {
        $serviceOrder = $this->serviceOrderRepository->find($id);

        if (!$serviceOrder) {
            return $this->json(['message' => 'Service order not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['guest'])) {
            $guest = $this->getDoctrine()->getRepository(Guest::class)->find($data['guest']);
            if ($guest) {
                $serviceOrder->setGuest($guest);
            } else {
                return $this->json(['message' => 'Invalid guest'], Response::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['service'])) {
            $service = $this->getDoctrine()->getRepository(Service::class)->find($data['service']);
            if ($service) {
                $serviceOrder->setService($service);
            } else {
                return $this->json(['message' => 'Invalid service'], Response::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['orderDate'])) {
            $orderDate = new \DateTime($data['orderDate']);
            $serviceOrder->setOrderDate($orderDate);
        }

        $this->entityManager->flush();

        return $this->json($serviceOrder);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_service_order', methods: ['DELETE'])]
    public function deleteServiceOrder(int $id): JsonResponse
    {
        $serviceOrder = $this->serviceOrderRepository->find($id);

        if (!$serviceOrder) {
            return $this->json(['message' => 'Service order not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($serviceOrder);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
