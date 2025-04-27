<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    /**
     * Get all reviews
     * @return JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $reviews = Review::all();
        return response()->json($reviews, Response::HTTP_OK);
    }

    /**
     * Get review by ID
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($review, Response::HTTP_OK);
    }

    /**
     * Create new review
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $requestData = $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'comment' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|between:1,5',
        ]);

        $guest = Guest::find($requestData['guest_id']);
        if (!$guest) {
            return response()->json(['error' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $review = $guest->reviews()->create($requestData);

        return response()->json($review, Response::HTTP_CREATED);
    }

    /**
     * Update review
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(string $id, Request $request): JsonResponse
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $requestData = $request->validate([
            'comment' => 'string|min:10|max:1000',
            'rating' => 'integer|between:1,5',
        ]);

        $review->update($requestData);

        return response()->json($review, Response::HTTP_OK);
    }
    /**
     * Delete review
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $review->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

