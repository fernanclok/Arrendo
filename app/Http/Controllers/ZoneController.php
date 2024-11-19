<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function getZones() {
        $zones = Zone::all();

        return response()->json($zones);
    }
}
