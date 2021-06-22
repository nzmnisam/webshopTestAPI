<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Categories;


class CategoriesController extends Controller
{

    public function show($slug)
    {

        $catagoryAndChildren = DB::select(DB::raw(
            "SELECT node.category_name, node.slug, node.id, (COUNT(parent.category_name) - (sub_tree.depth + 1)) AS depth
            FROM categories AS node, categories AS parent, categories AS sub_parent,
                (SELECT node.category_name, node.slug, node.id, (COUNT(parent.category_name) - 1) AS depth
                    FROM categories AS node, categories AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.slug = " . "'" . $slug . "'" .
                    " GROUP BY node.category_name
                    ORDER BY node.lft) AS sub_tree
            WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt 
            AND sub_parent.category_name = sub_tree.category_name
            GROUP BY node.category_name
            HAVING depth <= 1
            ORDER BY node.lft;"
        ));
   
        return response()->json(["categories" => $catagoryAndChildren]);
    }

    public function ancestors($category_id) {
        $ancestorsOfCategory = DB::select(DB::raw(
            "SELECT t0.category_name category, t0.slug slug, t0.id, (SELECT GROUP_CONCAT(t2.category_name ORDER BY t2.lft) 
            FROM categories t2 WHERE t2.lft < t0.lft AND t2.rgt > t0.rgt) ancestors,
            (SELECT GROUP_CONCAT(t2.slug ORDER BY t2.lft) 
            FROM categories t2 WHERE t2.lft < t0.lft AND t2.rgt > t0.rgt) slugs,
            (SELECT GROUP_CONCAT(t2.id ORDER BY t2.lft) 
            FROM categories t2 WHERE t2.lft < t0.lft AND t2.rgt > t0.rgt) ids
            FROM categories t0 WHERE t0.id = " . "'" . $category_id . "'" .
            " GROUP BY t0.category_name;"
        ));

        return response()->json(['ancestors' => $ancestorsOfCategory]);
    }

    public function showAll() {
        $allCategories = DB::select(DB::raw(
            "SELECT node.category_name, node.slug, node.id, (COUNT(parent.category_name) - 1) AS depth
            FROM categories AS node, categories AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            GROUP BY node.category_name
            ORDER BY node.lft;"
        ));
       return response()->json(['categories' => $allCategories]);
    }

    public function showSubCategories($slug) {
        $subCategories = DB::select(DB::raw(
            "SELECT node.category_name, node.id, node.slug, (COUNT(parent.category_name) - (sub_tree.depth + 1)) AS depth
            FROM categories AS node,
            categories AS parent,
            categories AS sub_parent,
            (
                    SELECT node.category_name, (COUNT(parent.category_name) - 1) AS depth
                    FROM categories AS node,
                    categories AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    AND node.slug= " . "'" .$slug . "'" .
                    "GROUP BY node.category_name
                    ORDER BY node.lft
            )AS sub_tree
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                AND sub_parent.category_name = sub_tree.category_name
            GROUP BY node.category_name
            ORDER BY node.lft;"));

            return response()->json(["subCategories"=>$subCategories]);

    }
}
