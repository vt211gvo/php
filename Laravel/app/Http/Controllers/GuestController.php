<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GuestController extends Controller
{
    /**
     * Get all guests
     * @return Collection
     */
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return Guest::all();
    }

    /**
     * Get guest by ID
     * @param Guest $guest
     * @return Guest
     */
    public function show(Guest $guest): Guest
    {
        return $guest;
    }

    /**
     * Create new guest
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request): mixed
    {
        $request->validate([
            'document_number' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
        ]);

        return Guest::create($request->all());
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
     * @return Response
     */
    public function destroy(Guest $guest): \Illuminate\Http\Response
    {
        $guest->delete();
        return response()->noContent();
    }
}
