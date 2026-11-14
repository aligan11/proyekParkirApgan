<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use Illuminate\Http\Request;

class DendaControllerApi extends Controller
{
    public function index()
    {
        $denda = Denda::latest()->get();

        return response()->json([
            'status' => 'success',
            'respose' => $denda,
        ], 200);
    }
    //
}
