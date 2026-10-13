<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use App\Models\Wcpu\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BarangayController extends Controller
{
    /**
     * Get all barangays for dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBarangays()
    {
        return \App\Models\Wcpu\Barangay::orderBy('name')->get(['id', 'code', 'name']);
    }

    /**
     * Return all barangays as JSON for dropdowns.
     */
    public function index()
    {
        return response()->json(Barangay::orderBy('name')->get(['id', 'code', 'name']));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
