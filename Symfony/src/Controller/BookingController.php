<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Guest;
use App\Entity\Bed;
use App\Repository\BookingRepository;
use App\Repository\GuestRepository;
use App\Repository\BedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/booking', name: 'booking_routes')]
class BookingController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private BookingRepository $bookingRepository;
    private GuestRepository $guestRepository;
    private BedRepository $bedRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
        GuestRepository $guestRepository,
        BedRepository $bedRepository
    ) {
        $this->entityManager = $entityManager;
        $this->bookingRepository = $bookingRepository;
        $this->guestRepository = $guestRepository;
        $this->bedRepository = $bedRepository;
    }

    #[Route('/', name: 'get_bookings', methods: ['GET'])]
    public function getBookings(): JsonResponse
    {
        $bookings = $this->bookingRepository->findAll();
        return $this->json($bookings);
    }

    #[Route('/{id}', name: 'get_booking', methods: ['GET'])]
    public function getBooking(int $id): JsonResponse
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            return $this->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($booking);
    }

    #[Route('/', name: 'create_booking', methods: ['POST'])]
    public function createBooking(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['guestId'])) {
            return $this->json(['message' => 'Guest ID is required.'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($data['bedId'])) {
            return $this->json(['message' => 'Bed ID is required.'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($data['checkinDate']) || empty($data['checkoutDate'])) {
            return $this->json(['message' => 'Check-in and Check-out dates are required.'], Response::HTTP_BAD_REQUEST);
        }

        $guest = $this->guestRepository->find($data['guestId']);
        if (!$guest) {
            return $this->json(['message' => 'Guest not found.'], Response::HTTP_NOT_FOUND);
        }

        $bed = $this->bedRepository->find($data['bedId']);
        if (!$bed) {
            return $this->json(['message' => 'Bed not found.'], Response::HTTP_NOT_FOUND);
        }

        $booking = new Booking();
        $booking->setGuest($guest);
        $booking->setBed($bed);
        $booking->setCheckinDate(new \DateTime($data['checkinDate']));
        $booking->setCheckoutDate(new \DateTime($data['checkoutDate']));
        $booking->setStatus($data['status']);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $this->json($booking, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_booking', methods: ['PATCH'])]
    public function updateBooking(Request $request, int $id): JsonResponse
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            return $this->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['guestId'])) {
            $guest = $this->guestRepository->find($data['guestId']);
            if (!$guest) {
                return $this->json(['message' => 'Guest not found.'], Response::HTTP_NOT_FOUND);
            }
            $booking->setGuest($guest);
        }

        if (isset($data['bedId'])) {
            $bed = $this->bedRepository->find($data['bedId']);
            if (!$bed) {
                return $this->json(['message' => 'Bed not found.'], Response::HTTP_NOT_FOUND);
            }
            $booking->setBed($bed);
        }

        if (isset($data['checkinDate'])) {
            $booking->setCheckinDate(new \DateTime($data['checkinDate']));
        }

        if (isset($data['checkoutDate'])) {
            $booking->setCheckoutDate(new \DateTime($data['checkoutDate']));
        }

        if (isset($data['status'])) {
            $booking->setStatus($data['status']);
        }

        $this->entityManager->flush();

        return $this->json($booking);
    }

    #[Route('/{id}', name: 'delete_booking', methods: ['DELETE'])]
    public function deleteBooking(int $id): JsonResponse
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            return $this->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($booking);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
