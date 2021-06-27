<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ImagesController;



class CartController extends Controller
{
    //
    public function getCartForUser($id) {
        $allFromCart = Cart::all();
        $productsInCart = [];
        $productsIdsString = "";
        // dd($id);
        if($id !== null) {
            $productsInCart = DB::select(DB::raw(
                "SELECT p.*, COUNT(p.id) AS 'quantity' FROM products p JOIN carts c ON (p.id = c.product_id) 
                WHERE c.user_id = " . $id . " GROUP BY(p.id)"));
        $productsIdsString = implode(',', array_map(function($product) { return $product->id; }, $productsInCart));
        //ovo ne treba da se radi!
        $imagesController = new ImagesController();
        $thumbnails = $imagesController->getThumbnailsForSimilar($productsIdsString);

        foreach($productsInCart as $productInCart) {
            foreach($thumbnails as $id=>$thumbnail) {
                if($productInCart->id === $id) {
                    $productInCart->thumbnail = $thumbnail;
                    continue;
                }
            }
        }
        

    } 
        return $productsInCart;
    }
    public function getCartForAdmin($id) {
        $allFromCart = Cart::all();
        $productsInCart = [];
        $productsIdsString = "";
        // dd($id);
        if($id !== null) {
            $productsInCart = DB::select(DB::raw(
                "SELECT p.*, COUNT(p.id) AS 'quantity' FROM products p JOIN carts c ON (p.id = c.product_id) 
                WHERE c.admin_id = " . $id . " GROUP BY(p.id)"));
            $productsIdsString = implode(',', array_map(function($product) { return $product->id; }, $productsInCart));
            //ovo ne treba da se radi!
            $imagesController = new ImagesController();
            $thumbnails = $imagesController->getThumbnailsForSimilar($productsIdsString);

        foreach($productsInCart as $productInCart) {
            foreach($thumbnails as $id=>$thumbnail) {
                if($productInCart->id === $id) {
                    $productInCart->thumbnail = $thumbnail;
                    continue;
                }
            }
        }
        

    } 
        return $productsInCart;
    }

    public function store(Request $request) {
        if($request['admin_id'] !== null) {
            $cart = new Cart;
            $cart->admin_id = $request['admin_id'];
            $cart->product_id = $request['product_id'];
            $cart->save();
            return response()->json('Saved succesfully for admin', 200);

        } 
        else if($request['user_id'] !== null) {
            $cart = new Cart;
            $cart->user_id = $request['user_id'];
            $cart->product_id = $request['product_id'];
            $cart->save();
            return response()->json('Saved succesfully for user', 200);

        }
        return response()->json('Bad request', 400);

    }

    //brisu se svi za taj product_id i user_id (X dugme)
    public function deleteFromCart(Request $request) {
        $response = '';
       if($request['user_id'] !== null) {
           $response = Cart::where('user_id', $request['user_id'])
                            ->where('product_id', $request['product_id'])
                            ->delete();
        } 
        else if($request['admin_id'] !== null) {
        $response = Cart::where('admin_id', $request['admin_id'])
                         ->where('product_id', $request['product_id'])
                         ->delete();
        }
        return $response; 
    }

    //isprazni korpu dugme, brisu se svi proizvodi za taj user_id
    public function deleteAllFromCart(Request $request) {
        $response = '';
       if($request['user_id'] !== null) {
           $response = Cart::where('user_id', $request['user_id'])
                            ->delete();
        } 
        else if($request['admin_id'] !== null) {
        $response = Cart::where('admin_id', $request['admin_id'])
                         ->delete();
        }
        return $response; 
        //ako vrati 0, nije obrisano ili nema recorda, ako vrati bilo sta drugo, toliko je obrisano
    }

    //brisanje po jednog
    public function deleteOneFromCart(Request $request) {
        $response = '';
        if($request['user_id'] !== null) {
            $response = Cart::where('user_id', $request['user_id'])
                                ->where('product_id', $request['product_id'])
                                ->first()->delete();
        }
        else if($request['admin_id'] !== null) {
            $response = Cart::where('admin_id', $request['admin_id'])
                                ->where('product_id', $request['product_id'])
                                ->first()->delete();
        }


        return $response;
    }
}
