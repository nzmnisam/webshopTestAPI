<?php

namespace App\Http\Controllers;

use App\Models\Models;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class ModelsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'model_path' => 'required|string',
            'model_name' => 'required|string',
            'slug' => 'required|string|unique:models,slug',
        ]);

        $modelPath =  $request['model_path'];

        $base64_model = $request['model'];
        $file_data = $base64_model;
        $safeName = $request['model_name'] . '.glb';
        Storage::disk('public')->put($modelPath . '/' . $safeName, base64_decode($file_data));

        $model = Models::create($request->all());
        return $model;
    }

    public function setProductId(Request $request, $id) {
        $model = Models::find($id);
        $product_id = $request['product_id'];
        $model->update(['product_id' => $product_id]);
        return $model;
    }

    public function getModelForProduct($product_id)
    {
        //find images for that product id
        $model = DB::select(DB::raw(
        "SELECT * FROM models where product_id = ".  "'" . $product_id  . "'" . ";"
        ));        

       
        if(!empty($model)) {
            $modelPath = $model[0]->model_path;
            $modelName = $model[0]->model_name;
            $b64Model = base64_encode(Storage::get('public/' . $modelPath . '/' . $modelName . '.glb'));

            return response()->json($b64Model);
        }

        return response()->json(["message" => "Nema modela za taj proizvod"], 404);
       





    }

    public function destroy($id)
    {
        $model = Models::find($id);

        $modelPath =  $model->model_path;
        $modelUrl = $modelPath . '/' . $model->model_name . '.glb';
        Storage::delete('public/' . $modelUrl);
        return Models::destroy($id);

    }
}
