<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notification', name: 'notification_routes')]
class NotificationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private NotificationRepository $notificationRepository;

    public function __construct(EntityManagerInterface $entityManager, NotificationRepository $notificationRepository)
    {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_notifications', methods: ['GET'])]
    public function getNotifications(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->notificationRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_notification', methods: ['GET'])]
    public function getNotification(int $id): JsonResponse
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification) {
            return $this->json(['message' => 'Notification not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($notification);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_notification', methods: ['POST'])]
    public function createNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['message']) || empty($data['sentAt']) || !isset($data['guest'])) {
            return $this->json(['message' => 'Message, sentAt, and guest are required.'], Response::HTTP_BAD_REQUEST);
        }

        $guest = $this->getDoctrine()->getRepository(Guest::class)->find($data['guest']);
        if (!$guest) {
            return $this->json(['message' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $notification = new Notification();
        $notification->setMessage($data['message']);
        $notification->setSentAt(new \DateTime($data['sentAt']));
        $notification->setGuest($guest);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $this->json($notification, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_notification', methods: ['PATCH'])]
    public function updateNotification(Request $request, int $id): JsonResponse
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification) {
            return $this->json(['message' => 'Notification not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['message'])) {
            $notification->setMessage($data['message']);
        }

        if (isset($data['sentAt'])) {
            $notification->setSentAt(new \DateTime($data['sentAt']));
        }

        if (isset($data['guest'])) {
            $guest = $this->getDoctrine()->getRepository(Guest::class)->find($data['guest']);
            if (!$guest) {
                return $this->json(['message' => 'Guest not found'], Response::HTTP_NOT_FOUND);
            }
            $notification->setGuest($guest);
        }

        $this->entityManager->flush();

        return $this->json($notification);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_notification', methods: ['DELETE'])]
    public function deleteNotification(int $id): JsonResponse
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification) {
            return $this->json(['message' => 'Notification not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($notification);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
