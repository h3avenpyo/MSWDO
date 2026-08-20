<?php

namespace App\Http\Controllers;

use App\Models\OnlineRequest;
use App\Models\OnlineRequestAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceRequestController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_for' => ['required', 'in:myself,child,parent,family,assisting'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'dob' => ['required', 'date'],
                'barangay' => ['required', 'string', 'max:255'],
                'contact_number' => ['required', 'string', 'max:20'],
                'email' => ['required', 'email', 'max:255'],
                'address' => ['nullable', 'string', 'max:500'],
                'service_type' => ['required', 'in:financial_assistance,social_case_study,senior_citizen,vawc,bcpc,others'],
                'assistance_type' => ['required', 'in:medical,educational,food,transportation,burial,livelihood,emergency,others'],
                'situation' => ['required', 'string', 'max:2000'],
                'documents' => ['nullable', 'array'],
                'documents.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'], // 10MB max per file
            ]);

            // Save to database
            $onlineRequest = OnlineRequest::create([
                'request_for' => $validated['request_for'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'dob' => $validated['dob'],
                'barangay' => $validated['barangay'],
                'contact_number' => $validated['contact_number'],
                'email' => $validated['email'],
                'address' => $validated['address'] ?? null,
                'service_type' => $validated['service_type'],
                'assistance_type' => $validated['assistance_type'],
                'situation' => $validated['situation'],
                'status' => 'pending',
            ]);

            // Handle file uploads
            if ($request->hasFile('documents')) {
                $files = $request->file('documents');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            $fileName = time() . '_' . $file->getClientOriginalName();
                            $filePath = $file->storeAs('online-request-attachments', $fileName, 'public');
                            
                            OnlineRequestAttachment::create([
                                'online_request_id' => $onlineRequest->id,
                                'file_name' => $file->getClientOriginalName(),
                                'file_path' => $filePath,
                                'file_type' => $file->getClientMimeType(),
                                'file_size' => $file->getSize(),
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Service request submitted successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
