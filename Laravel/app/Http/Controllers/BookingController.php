<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    public const ITEMS_PER_PAGE = 2;

    /**
     * Get all bookings
     * @param Request $request
     * @return JsonResponse
     * /bookings?guest_id=1&room=bathroom
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::query();

        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        if ($request->has('guest_id')) {
            $query->where('guest_id', $request->guest_id);
        }

        if ($request->has('room')) {
            $query->where('room', 'like', "%{$request->room}%");
        }

        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $bookings = $query->paginate(self::ITEMS_PER_PAGE);

        $bookings->appends($request->except('page'));

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
