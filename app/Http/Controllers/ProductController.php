<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Images;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::all();

    }

    public function showByCategory($category_id)
    {
        $products = DB::select(DB::raw(
            "SELECT * FROM products WHERE category_id = " . "'" . $category_id  . "'" 
        ));
        return $products;
    }

    public function showByCategories($categories_id) {
        // $category_ids = explode(",", $categoriesIds);
        $products = DB::select(DB::raw(
        "SELECT * FROM products WHERE category_id IN (". $categories_id .");
        "));

        return $products;

    }

    // public function showSimilar($product_id) {
    //     $similarProductIds = DB::select(DB::raw(
    //         "SELECT * FROM products_similar WHERE product1_id =" . "'" . $product_id ."'"));
    //     $similarProductIdsArray = [];
    //     foreach($similarProductIds as $similarProduct) {
    //         array_push($similarProductIdsArray, $similarProduct->product2_id);
    //     }    
    //     $similarProductIdsString = implode(",", $similarProductIdsArray);
    //     $similarProducts = DB::select(DB::raw(
    //         "SELECT * FROM products WHERE id IN" . "(" . $similarProductIdsString . ")"
            
    //     ));
    //     return $similarProducts;
    // }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:products,name',
            'slug' => 'required|string|unique:products,slug',
            'price' => 'required',
            'category_id' => 'required',
        ]);
        $product = Product::create($request->all());
        return $product;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = DB::select(DB::raw(
            "SELECT * from products WHERE slug = " . "'" . $slug  . "'" 
        ));

        if(sizeof($product) != 0) {
            $product = $product[0];
            $categoryTree = [];
            if($product->category_id != null) {
                $categoryTree = DB::select(DB::raw(
                    "SELECT parent.slug, parent.category_name FROM categories AS node, categories AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.id = ". $product->category_id . "
                    ORDER BY parent.lft"
                ));
            }
            
            $productImagesPath = [];
            $productImagesPath = DB::select(DB::raw(
                "SELECT * FROM images WHERE product_id = ". $product->id
            ));

            foreach($productImagesPath as $path) {
                $imageUrl = $path->img_path . '/' . $path->img_name . '.jpg';
                $imageUrl = Storage::url($imageUrl);
                $path->img_path = $imageUrl;
            }

            $productModelPath = [];
            $productModelPath = DB::select(DB::raw(
                "SELECT * FROM models WHERE product_id = ". $product->id
            ));

            foreach($productModelPath as $path) {
                $modelUrl = $path->model_path . '/' . $path->model_name . '.glb';
                $modelUrl = Storage::url($modelUrl);
                $path->model_path = $modelUrl;
            }

            $similarProducts = [];
            $similarProducts = DB::select(DB::raw(
                "SELECT * FROM products_similar WHERE product1_id = ". $product->id . " OR product2_id = ". $product->id
            ));

            $partOfProducts = [];
            $partOfProducts = DB::select(DB::raw(
                "SELECT * FROM products_part_of WHERE product1_id = ". $product->id . " OR product2_id = ". $product->id
            ));
            
            return response()->json([
                "product" => $product, 
                "category tree" => $categoryTree, 
                "product images" => $productImagesPath,
                "product model" => $productModelPath,
                "similar products" => $similarProducts,
                "part of products" => $partOfProducts,
            ], 200);
        }

        return response()->json(["message" => "Not Found!"], 404);
    }

    public function showById($id) {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if($product->name != $request['name']) {
        $productImages = Images::where('product_id', $id)->get();
            foreach($productImages as $productImage) {
                $pathArray = explode('/', $productImage->img_path);
                $pathArray[count($pathArray) - 1] = $request['name'];
                $updatedPath = implode('/', $pathArray);
                
                $updatedSlug = implode('-', explode(' ', $request['name']));
                $updatedSlug = $updatedSlug . '-' . $productImage->img_name . '.jpg';
                Storage::move('public/' . $productImage->img_path . '/'.$productImage->img_name . '.jpg', 'public/' . $updatedPath . '/'.$productImage->img_name . '.jpg');
                // Storage::deleteDirectory('public/' . $productImage->img_path);
                $productImage->update(['img_path' => $updatedPath, 'slug' => $updatedSlug]);
            }
        }

        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Product::destroy($id);
    }

    
    /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        return Product::where('name', 'like', '%'.$name.'%')->get();
    }
}
