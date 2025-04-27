<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    /**
     * Get all bookings
     * @return JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $bookings = Booking::all();
        return response()->json($bookings, Response::HTTP_OK);
    }

    /**
     * Get booking by ID
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($booking, Response::HTTP_OK);
    }

    /**
     * Create new booking
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $guest = Guest::find($requestData['guest_id']);
        if (!$guest) {
            return response()->json(['error' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $booking = $guest->bookings()->create($requestData);

        return response()->json($booking, Response::HTTP_CREATED);
    }

    /**
     * Update booking
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(string $id, Request $request): \Illuminate\Http\JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        $requestData = $request->validate([
            'start_date' => 'date',
            'end_date' => 'date',
        ]);

        $booking->update($requestData);

        return response()->json($booking, Response::HTTP_OK);
    }

    /**
     * Delete booking
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        $booking->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
