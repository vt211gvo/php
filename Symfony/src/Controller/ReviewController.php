<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/review', name: 'review_routes')]
class ReviewController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ReviewRepository $reviewRepository;

    public function __construct(EntityManagerInterface $entityManager, ReviewRepository $reviewRepository)
    {
        $this->entityManager = $entityManager;
        $this->reviewRepository = $reviewRepository;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'get_reviews', methods: ['GET'])]
    public function getReviews(Request $request): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? (int)$requestData['itemsPerPage'] : 10;
        $page = isset($requestData['page']) ? (int)$requestData['page'] : 1;

        $data = $this->reviewRepository->getAllByFilter($requestData, $itemsPerPage, $page);
        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/{id}', name: 'get_review', methods: ['GET'])]
    public function getReview(int $id): JsonResponse
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return $this->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($review);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/', name: 'create_review', methods: ['POST'])]
    public function createReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['comment']) || empty($data['rating']) || empty($data['guest'])) {
            return $this->json(['message' => 'Comment, rating, and guest are required.'], Response::HTTP_BAD_REQUEST);
        }

        $guest = $this->getDoctrine()->getRepository(Guest::class)->find($data['guest']);
        if (!$guest) {
            return $this->json(['message' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $review = new Review();
        $review->setComment($data['comment']);
        $review->setRating($data['rating']);
        $review->setGuest($guest);

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $this->json($review, Response::HTTP_CREATED);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'update_review', methods: ['PATCH'])]
    public function updateReview(Request $request, int $id): JsonResponse
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return $this->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['comment'])) {
            $review->setComment($data['comment']);
        }

        if (isset($data['rating'])) {
            $review->setRating($data['rating']);
        }

        if (isset($data['guest'])) {
            $guest = $this->getDoctrine()->getRepository(Guest::class)->find($data['guest']);
            if (!$guest) {
                return $this->json(['message' => 'Guest not found'], Response::HTTP_NOT_FOUND);
            }
            $review->setGuest($guest);
        }

        $this->entityManager->flush();

        return $this->json($review);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'delete_review', methods: ['DELETE'])]
    public function deleteReview(int $id): JsonResponse
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return $this->json(['message' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($review);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
