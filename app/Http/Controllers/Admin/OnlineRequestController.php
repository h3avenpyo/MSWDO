<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnlineRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class OnlineRequestController extends Controller
{
    public function index()
    {
        $onlineRequests = OnlineRequest::where('status', 'pending')
            ->whereNull('case_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $sixMonthsAgo = now()->subMonths(6);

        $onlineRequests->getCollection()->transform(function ($req) use ($sixMonthsAgo) {
            $name = strtolower(trim($req->first_name . ' ' . $req->last_name));

            $client = Client::whereRaw('LOWER(CONCAT(first_name, " ", last_name)) = ?', [$name])->first();
            $req->warning_existing = (bool) $client;

            $req->warning_recent = false;
            if ($client && $client->assistanceRecords()
                    ->where('release_date', '>=', $sixMonthsAgo->toDateString())
                    ->exists()) {
                $req->warning_recent = true;
            }
            if (!$req->warning_recent) {
                $req->warning_recent = OnlineRequest::where('id', '!=', $req->id)
                    ->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) = ?', [$name])
                    ->where('created_at', '>=', $sixMonthsAgo)
                    ->exists();
            }

            return $req;
        });

        return view('admin.social-case.online-requests', compact('onlineRequests'));
    }

    public function show($id)
    {
        $request = OnlineRequest::with('attachments')->find($id);
        if (!$request) {
            return response()->json(['error' => 'Request not found'], 404);
        }
        
        $attachmentsHtml = '';
        if ($request->attachments->count() > 0) {
            $attachmentsHtml = '<div style="margin-top: 12px;"><h4 style="margin: 0 0 8px 0; color: #1A237E; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Attached Files</h4><ul style="margin: 0; padding-left: 20px;">';
            foreach ($request->attachments as $attachment) {
                $fileUrl = asset('storage/' . $attachment->file_path);
                $attachmentsHtml .= '<li style="margin-bottom: 4px;"><a href="' . $fileUrl . '" target="_blank" style="color: #1A237E; text-decoration: underline;">' . $attachment->file_name . '</a> (' . $this->formatFileSize($attachment->file_size) . ')</li>';
            }
            $attachmentsHtml .= '</ul></div>';
        } else {
            $attachmentsHtml = '<div style="margin-top: 12px;"><p style="margin: 0; font-size: 14px; color: #6B7280;">No files attached</p></div>';
        }
        
        return response()->json([
            'id' => $request->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'service_type' => ucfirst(str_replace('_', ' ', $request->service_type)),
            'assistance_type' => ucfirst(str_replace('_', ' ', $request->assistance_type)),
            'barangay' => $request->barangay,
            'status' => ucfirst($request->status),
            'created_at' => $request->created_at->format('M d, Y g:i A'),
            'situation' => $request->situation ?? 'N/A',
            'notes' => $request->notes ?? 'N/A',
            'attachments_html' => $attachmentsHtml
        ]);
    }

    public function showDetails($id)
    {
        $request = OnlineRequest::with('attachments')->find($id);
        if (!$request) {
            return response()->json(['error' => 'Request not found'], 404);
        }
        
        $attachmentsHtml = '';
        if ($request->attachments->count() > 0) {
            $attachmentsHtml = '<div style="margin-top: 12px;"><h4 style="margin: 0 0 8px 0; color: #1A237E; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Attached Files</h4><ul style="margin: 0; padding-left: 20px;">';
            foreach ($request->attachments as $attachment) {
                $fileUrl = asset('storage/' . $attachment->file_path);
                $attachmentsHtml .= '<li style="margin-bottom: 4px;"><a href="' . $fileUrl . '" target="_blank" style="color: #1A237E; text-decoration: underline;">' . $attachment->file_name . '</a> (' . $this->formatFileSize($attachment->file_size) . ')</li>';
            }
            $attachmentsHtml .= '</ul></div>';
        } else {
            $attachmentsHtml = '<div style="margin-top: 12px;"><p style="margin: 0; font-size: 14px; color: #6B7280;">No files attached</p></div>';
        }
        
        return response()->json([
            'id' => $request->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'service_type' => ucfirst(str_replace('_', ' ', $request->service_type)),
            'assistance_type' => ucfirst(str_replace('_', ' ', $request->assistance_type)),
            'barangay' => $request->barangay,
            'status' => ucfirst($request->status),
            'created_at' => $request->created_at->format('M d, Y g:i A'),
            'situation' => $request->situation ?? 'N/A',
            'notes' => $request->notes ?? 'N/A',
            'attachments_html' => $attachmentsHtml
        ]);
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function archive($id)
    {
        $request = OnlineRequest::find($id);
        if (!$request) {
            return response()->json(['success' => false, 'message' => 'Request not found'], 404);
        }

        if ($request->status === 'archived') {
            return response()->json(['success' => false, 'message' => 'Request is already archived'], 400);
        }

        $request->status = 'archived';
        $request->save();

        return response()->json(['success' => true, 'message' => 'Request archived successfully']);
    }

    public function accept($id)
    {
        $request = OnlineRequest::with('attachments')->find($id);
        if (!$request) {
            return response()->json(['success' => false, 'message' => 'Request not found'], 404);
        }

        if ($request->status === 'approved') {
            return response()->json(['success' => false, 'message' => 'Request is already approved'], 400);
        }

        $request->status = 'approved';
        $request->save();

        // Send email notification
        try {
            \Mail::raw(
                "Dear {$request->first_name} {$request->last_name},\n\n" .
                "We are pleased to inform you that your online service request has been approved.\n\n" .
                "Request Details:\n" .
                "- Service Type: " . ucfirst(str_replace('_', ' ', $request->service_type)) . "\n" .
                "- Assistance Type: " . ucfirst(str_replace('_', ' ', $request->assistance_type)) . "\n" .
                "- Barangay: {$request->barangay}\n\n" .
                "Please bring the hardcopy of the required documents to the MSWDO office for further processing.\n\n" .
                "If you have any questions, please visit the MSWDO Silang office for assistance.\n\n" .
                "Thank you,\n" .
                "MSWDO Silang",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Your Service Request Has Been Approved - MSWDO Silang');
                }
            );
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send email: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Request accepted successfully and email notification sent']);
    }

    public function decline(Request $httpRequest, $id)
    {
        $httpRequest->validate([
            'reason' => 'required|string|max:500',
        ]);

        $onlineRequest = OnlineRequest::find($id);
        if (!$onlineRequest) {
            return response()->json(['success' => false, 'message' => 'Request not found'], 404);
        }

        if ($onlineRequest->status === 'rejected') {
            return response()->json(['success' => false, 'message' => 'Request is already declined'], 400);
        }

        $reason = trim($httpRequest->input('reason'));

        $onlineRequest->status = 'rejected';
        $onlineRequest->notes = $reason;
        $onlineRequest->save();

        // Send decline email notification
        try {
            \Mail::raw(
                "Dear {$onlineRequest->first_name} {$onlineRequest->last_name},\n\n" .
                "We regret to inform you that your online service request has been declined.\n\n" .
                "Request Details:\n" .
                "- Service Type: " . ucfirst(str_replace('_', ' ', $onlineRequest->service_type)) . "\n" .
                "- Assistance Type: " . ucfirst(str_replace('_', ' ', $onlineRequest->assistance_type)) . "\n" .
                "- Barangay: {$onlineRequest->barangay}\n\n" .
                "Reason for Decline:\n" .
                "{$reason}\n\n" .
                "If you have any questions, please visit the MSWDO Silang office for assistance.\n\n" .
                "Thank you,\n" .
                "MSWDO Silang",
                function ($message) use ($onlineRequest) {
                    $message->to($onlineRequest->email)
                        ->subject('Your Service Request Has Been Declined - MSWDO Silang');
                }
            );
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send decline email: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Request declined successfully']);
    }

    public function accepted()
    {
        $acceptedRequests = OnlineRequest::where('status', 'approved')
            ->whereNull('case_id')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.social-case.online-requests-accepted', compact('acceptedRequests'));
    }

    public function rejected()
    {
        $rejectedRequests = OnlineRequest::where('status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.social-case.online-requests-rejected', compact('rejectedRequests'));
    }
}
