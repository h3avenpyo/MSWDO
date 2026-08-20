<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnlineRequest;
use Illuminate\Http\Request;

class OnlineRequestController extends Controller
{
    public function index()
    {
        $onlineRequests = OnlineRequest::where('status', '!=', 'archived')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
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
}
