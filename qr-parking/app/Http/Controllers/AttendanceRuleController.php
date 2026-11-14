<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRuleRequest;
use App\Http\Requests\UpdateAttendanceRuleRequest;
use App\Http\Resources\AttendanceRuleResource;
use App\Models\AttendanceRule;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AttendanceRuleController extends Controller
{
    use ApiResponse;
    /**
     * Mendapatkan semua aturan absensi
     */
    public function index(): JsonResponse
    {
        $perPage = request()->input('per_page') ?? 10;
        // dd($perPage);
        $rules = AttendanceRule::query()->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->paginate($perPage);

        if(!$rules){
            return $this->errorResponse('Data tidak ditemukan',404);
        }

        return $this->collectionResponse(AttendanceRuleResource::collection($rules), 'Berhasil mengembalikan aturan absensi.');
    }

    /**
     * Menyimpan aturan absensi baru
     */
    public function store(StoreAttendanceRuleRequest $request): JsonResponse
    {
        $rule = AttendanceRule::create($request->validated());
        return $this->successResponse(new AttendanceRuleResource($rule), 'Aturan absensi berhasil dibuat.', 201);
    }

    /**
     * Menampilkan detail aturan absensi
     */
    public function show(AttendanceRule $attendanceRule): JsonResponse
    {
        return $this->successResponse(new AttendanceRuleResource($attendanceRule), 'Berhasil mengambil aturan absensi.');
    }

    /**
     * Mengupdate aturan absensi
     */
    public function update(UpdateAttendanceRuleRequest $request, AttendanceRule $attendanceRule): JsonResponse
    {
        $attendanceRule->update($request->validated());

        return $this->successResponse(new AttendanceRuleResource($attendanceRule), 'Berhasil mempebarui aturan absensi.');
    }

    /**
     * Menghapus aturan absensi
     */
    public function destroy(AttendanceRule $attendanceRule): JsonResponse
    {
        $attendanceRule->delete();

        return $this->successResponse(message: 'Berhasil menghapus aturan absensi.');
    }

    /**
     * Mengaktifkan aturan absensi
     */
    public function activate(AttendanceRule $attendanceRule): JsonResponse
    {
        AttendanceRule::where('id', '!=', $attendanceRule->id)->update(['is_active' => false]);

        dd($attendanceRule);
        $attendanceRule->update(['is_active' => true]);

        return $this->successResponse(new AttendanceRuleResource($attendanceRule), 'Berhasil mengakifkan aturan absensi.');
    }

    /**
     * Mendapatkan aturan aktif
     */
    public function active(): JsonResponse
    {
        $rule = AttendanceRule::where('is_active', true)->first();

        if (!$rule) {
            return $this->errorResponse('Tidak ada aturan absensi yang aktif.', 404);
        }
        return $this->successResponse(new AttendanceRuleResource($rule), 'Berhasil mengambil aturan absensi yang aktif.');
    }
}
