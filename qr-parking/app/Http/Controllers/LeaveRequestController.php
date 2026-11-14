<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\IsEmpty;

use function PHPUnit\Framework\isNull;

class LeaveRequestController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $user = Auth::user();
        $perPage = request()->input('per_page') ?? 10;
        $leaveRequests = LeaveRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        if (!$leaveRequests) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        // return response()->json($leaveRequests);
        return $this->collectionResponse(LeaveRequestResource::collection($leaveRequests), 'Berhasil mendapatkan data.');
    }

    public function store(StoreLeaveRequest $request)
    {
        $request->validated();
        $user = Auth::user();

        $overlappingRequest = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();

        if ($overlappingRequest) {
            return response()->json([
                'message' => 'You already have an approved leave request for the selected period'
            ], 400);
        }

        $leaveRequest = new LeaveRequest();
        $leaveRequest->user_id = $user->id;
        $leaveRequest->leave_type = $request->leave_type;
        $leaveRequest->start_date = $request->start_date;
        $leaveRequest->end_date = $request->end_date;
        $leaveRequest->reason = $request->reason;
        $leaveRequest->status = 'pending';

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('leave_attachments', 'public');
            $leaveRequest->attachment_url = $path;
        }

        $leaveRequest->save();

        return response()->json([
            'message' => 'Leave request submitted successfully',
            'leave_request' => $leaveRequest
        ], 201);
    }

    public function show(string $id)
    {
        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }
        if ($leaveRequest->user_id != Auth::id()) {
            return $this->errorResponse('Unauthorized', 403);
            // return response()->json(['message' => 'Unauthorized'], 403);
        }

        // if ($leaveRequest->user_id != Auth::id() && !Auth::user()->isAdmin()) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        return $this->successResponse(new LeaveRequestResource($leaveRequest), 'Berhasil mendapatkan data.');
    }

    public function cancel($id)
    {
        $leaveRequest = LeaveRequest::find($id);

        if (!$leaveRequest) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }

        if ($leaveRequest->user_id != Auth::id()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($leaveRequest->status != 'pending') {
            return $this->errorResponse('Hanya status pending yang bisa dibatalkan.');
        }

        $leaveRequest->update(['status' => 'canceled']);

        return $this->successResponse('Berhasil membatalkan permintaan cuti.');
    }

    public function pendingRequests()
    {
        $perPage = request()->input('per_page') ?? 10;
        $requests = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        if (!$requests) {
            return $this->errorResponse('Data tidak ditemukan.', 404);
        }
        return $this->collectionResponse(LeaveRequestResource::collection($requests), 'Berhasil mendapatkan data.');

        // return $this->successResponse(LeaveRequestResource::collection($requests),'Berhasil mendapatkan data.');
    }

    public function approveRequest($id)
    {

        $leaveRequest = LeaveRequest::find($id);

        if ($leaveRequest == null) {
            return $this->errorResponse(message: 'Data tidak ada', code: 404);
        }

        if ($leaveRequest->status != 'pending') {
            return $this->errorResponse('Hanya permintan cuti tertunda yang dapat disetujui.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id()
        ]);

        return $this->successResponse(message: 'Berhasil mensetujui permintaan cuti.', code: 201);
    }

    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status != 'pending') {
            return response()->json([
                'message' => 'Only pending leave requests can be rejected'
            ], 400);
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Leave request rejected successfully'
        ]);
    }
}
