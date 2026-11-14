<?php

namespace App\Http\Controllers\ControllersWeb;

use Illuminate\Http\Request;
use App\Models\Parkir;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ParkirExport;
use Carbon\Carbon;

class ParkirExportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $from = $request->from;
        $to = $request->to;

        return Excel::download(new ParkirExport($from, $to), 'data_parkir_' . now()->format('Ymd_His') . '.xlsx');
    }
}
