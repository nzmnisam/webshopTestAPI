<?php

namespace App\Http\Controllers;

use App\Models\Mesto;

class MestosController extends Controller
{
    public function index() {
        $cities = Mesto::all();
        return response()->json(['cities'=>$cities], 200);
    }
}