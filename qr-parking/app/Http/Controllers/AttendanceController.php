<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInOutRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceRule;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNull;

class AttendanceController extends Controller
{
    use ApiResponse;

    /**
     * Menyimpan absensi masuk
     * @param \App\Http\Requests\CheckInOutRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function checkIn(CheckInOutRequest $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();
        $existingAttendance = Attendance::where('user_id', $user->id)->whereDate('attendance_date', $today)->first();

        if ($existingAttendance && $existingAttendance->check_in_time) {
            return $this->errorResponse('Anda sudah absen hari ini.');
        }

        $rule = AttendanceRule::where('is_active', true)->first();
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $rule->work_location_latitude, $rule->work_location_longitude);

        if ($distance > $rule->max_distance) {
            return $this->errorResponse('Anda terlalu jauh dari lokasi yang telah ditentukan.', 400, [
                'jarak' => $distance,
                'maksimal' => $rule->max_distance,
            ]);
        }

        $photoPath = $request->file('photo')->store('attendance_photos', 'public');
        $checkInStatus = 'on_time';
        $startTime = Carbon::parse($rule->start_time);
        if ($now->gt($startTime->addMinutes($rule->late_tolerance))) {
            $checkInStatus = 'late';
        }

        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'attendance_date' => $today,
            ],
            [
                'attendance_type' => 'work',
                'check_in_time' => $now->toTimeString(),
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_in_location' => $rule->work_location_name,
                'check_in_photo_url' => $photoPath,
                'check_in_status' => $checkInStatus,
                'notes' => $request->notes,
            ],
        );

        return $this->successResponse(new AttendanceResource($attendance), 'Berhasil melakukan absensi masuk.', 201);
    }

    /**
     * Menyimpan absensi pulang
     * @param \App\Http\Requests\CheckInOutRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function checkOut(CheckInOutRequest $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('attendance_date', $today)->first();

        if (!$attendance || !$attendance->check_in_time) {
            return $this->errorResponse('Anda harus melakukan absen masuk terlebih dahulu.');
        }

        if ($attendance->check_out_time) {
            return $this->errorResponse('Anda telah melakukan absen pulang hari ini.');
        }

        $rule = AttendanceRule::where('is_active', true)->first();
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $rule->work_location_latitude, $rule->work_location_longitude);

        if ($distance > $rule->max_distance) {
            return $this->errorResponse('Anda terlalu jauh dari lokasi yang telah ditentukan.', 400, [
                'jarak' => $distance,
                'maksimal' => $rule->max_distance,
            ]);
        }

        $photoPath = $request->file('photo')->store('attendance_photos', 'public');

        $checkOutStatus = 'on_time';
        $endTime = Carbon::parse($rule->end_time);
        if ($now->lt($endTime->subMinutes($rule->early_tolerance))) {
            $checkOutStatus = 'early';
        }

        $attendance->update([
            'check_out_time' => $now->toTimeString(),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out_location' => $rule->work_location_name,
            'check_out_photo_url' => $photoPath,
            'check_out_status' => $checkOutStatus,
            'notes' => $request->notes ?: $attendance->notes,
        ]);

        return $this->successResponse(new AttendanceResource($attendance), 'Berhasil melakukan absensi pulang.', 201);
    }

    /**
     * Mendapatkan data absensi
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getTodayAttendance()
    {
        $user = Auth::user();
        // $user = Auth::guard('sanctum')->user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return $this->errorResponse(message: 'Anda belum melakukan absensi hari ini.', code: 404);
        }

        return $this->successResponse(new AttendanceResource($attendance), 'Berhasil mendapatkan data absensi hari ini.');
    }

    /**
     * Mendapatkan riwayat absensi
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAttendanceHistory(Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000',
        ]);

        // $v = Validator::make($request->all(),[
        //         'month' => 'nullable|integer|min:1|max:12',
        //         'year' => 'nullable|integer|min:2000',
        // ]);

        // if(!$v && $v->errors()){
        //     return $this->errorResponse('Error field',400,$v->errors());
        // }

        $user = Auth::user();
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $perPage = request()->input('per_page') ?? 10;
        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->orderBy('attendance_date', 'desc')
            ->paginate($perPage);

        if (!$attendances) {
            return $this->errorResponse(message: 'Data tidak ditemukan', code: 404);
        }

        // return $this->successResponse(new AttendanceResource($attendances),'Berhasil mendapatkan riwayat absensi.');
        return $this->collectionResponse(AttendanceResource::collection($attendances), 'Berhasil mendapatkan riwayat absensi.');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return $miles * 1609.344;
    }
}
