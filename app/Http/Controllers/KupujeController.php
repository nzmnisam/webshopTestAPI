<?php

namespace App\Http\Controllers;

use App\Models\Kupuje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KupujeController extends Controller
{
    //
    public function store(Request $request) {
        $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
        ]);

        return Kupuje::create($request->all());
    }

    public function index() {
        return Kupuje::all();
    }

    public function delete(Request $request) {
        $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
        ]);

        $deleted = 0;
        $deleted = DB::delete(DB::raw("
            DELETE FROM kupujes WHERE user_id = " . $request->user_id . " AND product_id = " . $request->product_id
        ));

        return response()->json([
            'deleted' => $deleted,
        ]);
    }

    public function deleteByUser($id) {
        $deleted = 0;
        $deleted = DB::delete(DB::raw("
            DELETE FROM kupujes where user_id = " . $id
        ));
        return response()->json([
            'deleted' => $deleted,
        ]);
    }

    public function deleteByProduct($id) {
        $deleted = false;
        $deleted = DB::delete(DB::raw("
            DELETE FROM kupujes where product_id = " . $id
        ));
        return response()->json([
            'deleted' => $deleted,
        ]);
    }

    public function showByUser($id) {
        
        $products = [];
        $products = DB::select(DB::raw("
            SELECT * from kupujes where user_id = " . $id
        ));

        return response()->json([
            'products' => $products,
        ], 200);
    }

    public function showByProduct($id) {
        $users = [];
        $users = DB::select(DB::raw("
            SELECT * from kupujes where product_id = " . $id
        ));

        return response()->json([
            "users" => $users,
        ], 200);
    }
}
