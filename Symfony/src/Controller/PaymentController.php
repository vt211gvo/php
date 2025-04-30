<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/payment', name: 'payment_routes')]
class PaymentController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PaymentRepository $paymentRepository;

    public function __construct(EntityManagerInterface $entityManager, PaymentRepository $paymentRepository)
    {
        $this->entityManager = $entityManager;
        $this->paymentRepository = $paymentRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_payments', methods: ['GET'])]
    public function getPayments(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->paymentRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_payment', methods: ['GET'])]
    public function getPayment(int $id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return $this->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($payment);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_payment', methods: ['POST'])]
    public function createPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['amount']) || empty($data['paymentDate']) || empty($data['method']) || empty($data['booking'])) {
            return $this->json(['message' => 'Amount, paymentDate, method, and booking are required.'], Response::HTTP_BAD_REQUEST);
        }

        $booking = $this->getDoctrine()->getRepository(Booking::class)->find($data['booking']);
        if (!$booking) {
            return $this->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        $payment = new Payment();
        $payment->setAmount($data['amount']);
        $payment->setPaymentDate(new \DateTime($data['paymentDate']));
        $payment->setMethod($data['method']);
        $payment->setBooking($booking);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $this->json($payment, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_payment', methods: ['PATCH'])]
    public function updatePayment(Request $request, int $id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return $this->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['amount'])) {
            $payment->setAmount($data['amount']);
        }

        if (isset($data['paymentDate'])) {
            $payment->setPaymentDate(new \DateTime($data['paymentDate']));
        }

        if (isset($data['method'])) {
            $payment->setMethod($data['method']);
        }

        if (isset($data['booking'])) {
            $booking = $this->getDoctrine()->getRepository(Booking::class)->find($data['booking']);
            if (!$booking) {
                return $this->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
            }
            $payment->setBooking($booking);
        }

        $this->entityManager->flush();

        return $this->json($payment);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_payment', methods: ['DELETE'])]
    public function deletePayment(int $id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return $this->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($payment);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
