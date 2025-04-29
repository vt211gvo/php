<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestController extends Controller
{
    public const ITEMS_PER_PAGE = 3;

    /**
     * Get all guests with optional filters and pagination.
     * * /guests?document_number=123&phone=380
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Guest::query();

        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        if ($request->has('document_number')) {
            $query->where('document_number', 'like', "%{$request->document_number}%");
        }

        if ($request->has('phone')) {
            $query->where('phone', 'like', "%{$request->phone}%");
        }

        $guests = $query->paginate(self::ITEMS_PER_PAGE);
        $guests->appends($request->except('page'));

        return response()->json($guests, Response::HTTP_OK);
    }

    /**
     * Get guest by ID
     * @param Guest $guest
     * @return JsonResponse
     */
    public function show(Guest $guest): JsonResponse
    {
        return response()->json($guest, Response::HTTP_OK);
    }

    /**
     * Create new guest
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $guest = new Guest();
        $guest->document_number = $validated['document_number'];
        $guest->phone = $validated['phone'];
        $guest->save();

        return response()->json($guest, Response::HTTP_CREATED);
    }


    /**
     * Update guest
     * @param Request $request
     * @param Guest $guest
     * @return Guest
     */
    public function update(Request $request, Guest $guest): Guest
    {
        $guest->update($request->all());
        return $guest;
    }

    /**
     * Delete guest
     * @param Guest $guest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Guest $guest): \Illuminate\Http\Response
    {
        $guest->delete();
        return response()->noContent();
    }
}
