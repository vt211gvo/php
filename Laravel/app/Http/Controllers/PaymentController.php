<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Get all payments
     * @return JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $payments = Payment::all();
        return response()->json($payments, Response::HTTP_OK);
    }

    /**
     * Get payment by ID
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($payment, Response::HTTP_OK);
    }

    /**
     * Create new payment
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $requestData = $request->validate([
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'method' => 'required|string',
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::find($requestData['booking_id']);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        $payment = $booking->payments()->create($requestData);

        return response()->json($payment, Response::HTTP_CREATED);
    }

    /**
     * Update payment
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(string $id, Request $request): JsonResponse
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        $requestData = $request->validate([
            'amount' => 'numeric',
            'payment_date' => 'date',
            'method' => 'string',
        ]);

        $payment->update($requestData);

        return response()->json($payment, Response::HTTP_OK);
    }

    /**
     * Delete payment
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        }

        $payment->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

