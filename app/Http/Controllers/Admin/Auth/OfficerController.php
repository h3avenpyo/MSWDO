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

        return view('admin.add-officers', compact('officerCreated'));
    }

    public function officersDirectory()
    {
        $officers = User::
            where('email', '!=', 'admin@mswdo.test')
            ->select('id', 'name', 'email', 'role', 'phone', 'created_at', 'status')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.officers-directory', compact('officers'));
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
            'status' => ['nullable', 'in:active,inactive'],
            'signature_position' => ['nullable', 'in:osca_head,mswdo_officer,mswdo_staff'],
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
            'status' => $request->status === 'inactive' ? \App\Enums\UserStatus::Inactive : \App\Enums\UserStatus::Active,
            'signature_position' => $request->signature_position,
            'signature_image' => $signatureImagePath,
        ]);

        return redirect()->route('admin.add-officers')->with('success', 'Officer created successfully.')->with('officer_created', true);
    }

    public function editOfficer($id)
    {
        $officer = User::findOrFail($id);
        return view('admin.edit-officer', compact('officer'));
    }

    public function updateOfficer(Request $request, $id)
    {
        $officer = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($officer) {
                $exists = User::
                    whereRaw('LOWER(name) = ?', [strtolower($value)])
                    ->where('id', '!=', $officer->id)
                    ->exists();
                if ($exists) {
                    $fail('An officer with this name already exists.');
                }
            }],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($officer->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
            'signature_position' => ['nullable', 'in:osca_head,mswdo_officer,mswdo_staff'],
            'signature_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $signatureImagePath = $officer->signature_image;
        if ($request->hasFile('signature_image')) {
            // Delete old signature if exists
            if ($officer->signature_image && file_exists(public_path($officer->signature_image))) {
                unlink(public_path($officer->signature_image));
            }
            $file = $request->file('signature_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/signatures'), $filename);
            $signatureImagePath = 'images/signatures/' . $filename;
        }

        $officer->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status === 'active' ? \App\Enums\UserStatus::Active : \App\Enums\UserStatus::Inactive,
            'signature_position' => $request->signature_position,
            'signature_image' => $signatureImagePath,
        ]);

        return redirect()->route('admin.officers-directory')->with('success', 'Officer updated successfully.');
    }

    public function deactivateOfficer($id)
    {
        $officer = User::findOrFail($id);
        $officer->status = \App\Enums\UserStatus::Inactive;
        $officer->save();
        return redirect()->route('admin.officers-directory')->with('success', 'Officer deactivated successfully.');
    }

    public function activateOfficer($id)
    {
        $officer = User::findOrFail($id);
        $officer->status = \App\Enums\UserStatus::Active;
        $officer->save();
        return redirect()->route('admin.officers-directory')->with('success', 'Officer activated successfully.');
    }
}
