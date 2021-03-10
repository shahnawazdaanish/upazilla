<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Measurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UtilityController extends Controller
{

    public function getUnits(Request $request) {
        return Response::json(Measurement::all(), 200);
    }
}
