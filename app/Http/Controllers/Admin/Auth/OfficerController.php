<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OfficerController extends Controller
{
    public function addOfficers()
    {
        $officerCreated = session('officer_created', false);

        if ($officerCreated) {
            session()->forget('officer_created');
        }

        $officers = User::
            orderByDesc('created_at')
            ->get();

        return view('admin.add-officers', compact('officers', 'officerCreated'));
    }

    public function storeOfficer(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $exists = User::
                    whereRaw('LOWER(name) = ?', [strtolower($value)])
                    ->exists();
                if ($exists) {
                    $fail('An officer with this name already exists.');
                }
            }],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
            'signature_position' => ['nullable', 'in:osca_head,mswdo_officer'],
            'signature_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $signatureImagePath = null;
        if ($request->hasFile('signature_image')) {
            $file = $request->file('signature_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/signatures'), $filename);
            $signatureImagePath = 'images/signatures/' . $filename;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status,
            'signature_position' => $request->signature_position,
            'signature_image' => $signatureImagePath,
        ]);

        return redirect()->route('admin.add-officers')->with('success', 'Officer created successfully.')->with('officer_created', true);
    }
}
