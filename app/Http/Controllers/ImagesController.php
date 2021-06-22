<?php

namespace App\Http\Controllers;

use App\Models\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class ImagesController extends Controller
{

    public function index()
    {
        // return Images::all();
        //get image path
        //locate image in storage
        //encode image to base64
        //return object with image in base64 and image id
        $imagesFromDatabase = Images::all();
        return $imagesFromDatabase;

    }


    public function getThumbnailsForSimilar($productIds) {
        $imagesFromDatabase = DB::select(DB::raw("SELECT * FROM images WHERE product_id IN " . "("  . $productIds . ")"));

        $return_thumbnails = [];
        $product_ids = [];
        foreach($imagesFromDatabase as $thumbnail) {
            if(!in_array($thumbnail->product_id, $product_ids)) {
                array_push($product_ids, $thumbnail->product_id);
                $thumbnail_storage = base64_encode(Storage::get('public/'.$thumbnail->img_path . '/' . $thumbnail->img_name.".jpg"));
                array_push($return_thumbnails, $thumbnail_storage);
            }


            // array_push($product_ids, $image->product_id);
        }
        $thumbnails_complete = array_combine($product_ids, $return_thumbnails);

        return $thumbnails_complete;
    }

    public function getThumbnails()
    {
        //get image path
        $imagesFromDatabase = Images::all();
        $imagesPath = [];
        $imagesName = [];
        $productId = [];
        foreach($imagesFromDatabase as $image) {
            $imagePath = $image->img_path;
            if(!in_array($imagePath, $imagesPath)) {
                array_push($imagesPath, $image->img_path);
                array_push($imagesName, $image->img_name);
                array_push($productId, $image->product_id);
            }
        }
        //zasto salje img2 za prvi path?

        //locate image in storage
        $images = [];
        $imgNameIndex = 0;
        foreach($imagesPath as $imagePath) {
            $image = base64_encode(Storage::get('public/' . $imagePath . '/' . $imagesName[$imgNameIndex] . '.jpg'));
            array_push($images, $image);
            $imgNameIndex++;
        }
        //encode images to base64
        //return array of objects with image in base64 and product id
        // dd($images);
        // dd($productId);
        $imagesWithIdAndb64Image = array_combine($productId, $images);
    
        // return [$imagesPath, $imagesName, $imagesId, $images];
        // return $imagesWithIdAndb64Image;
        return response()->json($imagesWithIdAndb64Image, 200);

    }

    public function getImagesForProduct($product_id)
    {
        //find images for that product id
        $productImages = DB::select(DB::raw(
        "SELECT * FROM images where product_id = ".  "'" . $product_id  . "'" . ";"
        ));        

        $images = [];
        $imageIds = [];
        foreach($productImages as $image) {
            $imageId = $image->id;
            $imagePath = $image->img_path;
            $imageName = $image->img_name;
            $b64Image = base64_encode(Storage::get('public/' . $imagePath . '/' . $imageName . '.jpg'));
            array_push($images, $b64Image);
            array_push($imageIds, $imageId);
        }
        $response = array_combine($imageIds, $images);


        return $images;
    }
    public function store(Request $request)
    {
        $request->validate([
            'img_path' => 'required|string',
            'img_name' => 'required|string',
            'slug' => 'required|string',
            'image' => 'required',

        ]);

        $imgPath =  $request['img_path'];

        $base64_image = $request['image'];
        @list($type, $file_data) = explode(';', $base64_image);
        @list(, $file_data) = explode(',', $file_data);

        $safeName = $request['img_name'] . '.jpg';


        Storage::disk('public')->put($imgPath . '/' . $safeName, base64_decode($file_data));

        $image = Images::create($request->all());
        return $image;
    }

    public function setProductId(Request $request) {
        $imageIds= $request['image_ids'];
        $product_id = $request['product_id'];

        foreach($imageIds as $id) {
            $image = Images::find($id);
            $image->update(['product_id' => $product_id]);
        }


        $product_images = $this->getImagesForProduct($product_id);

        return $product_images;
    }

    public function setProductIdUpdate(Request $request) {
        $b64Images = $request['b64_images'];
        $product_id = $request['product_id'];

        // $product_images = DB::select(DB::raw("SELECT * FROM images WHERE product_id = " . "'" . $product_id . "'"));
        // $product_images = $this->getImagesForProduct($product_id);
       
        // $product_images = $this->getImagesForProduct($product_id);
        // //da li se nalazi u bazi
        // $numberOfImagesInDatabase = count($product_images);
        // $matchingImages = [];
        // foreach($b64Images as $b64Image) {
        //     if(in_array($product_images, $b64Image)) {
        //         array_push($matchingImages, $b64Image);
        //     }
        // }
        // $numberOfMatchingImages = count($matchingImages);
        // //ako je broj slika u bazi veci od broja slika koje se poklapaju
        // //treba obrisati slike iz baze koje se ne poklapaju
        // if($numberOfImagesInDatabase > $numberOfMatchingImages) {
        //     $missingImages = array_diff($product_images, $matchingImages);
        //     $product_images = array_diff($product_images, $missingImages);
        // }

        //brisanje slika iz memorije
        $productImages = DB::select(DB::raw(
            "SELECT * FROM images where product_id = ".  "'" . $product_id  . "'" . ";"
        ));  

        foreach($productImages as $image) {
            $imagePath = $image->img_path;
            $imageName = $image->img_name;
            $imgUrl = $imagePath . '/' . $imageName . '.jpg';
            Storage::delete('public/'.$imgUrl);
        }            
        
        
        //brisanje slika iz baze
        DB::select(DB::raw("DELETE FROM images WHERE product_id = " . "'". $product_id . "'"));
        
        $new_images = DB::select(DB::raw("SELECT id FROM images WHERE product_id IS " . "NULL"));
        foreach($new_images as $new_image) {
            $image = Images::find($new_image->id);
            // return $image;
            $image->update(['product_id' => $product_id]);
        }

        return Images::all();
        
    }

    public function destroy($id)
    {
        $image = Images::find($id);

        $imgPath =  $image->img_path;
        $imgUrl = $imgPath . '/' . $image->img_name . '.jpg';
        Storage::delete('public/'.$imgUrl);
        return Images::destroy($id);

    }
}
