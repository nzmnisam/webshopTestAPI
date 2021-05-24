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
            "SELECT node.category_name, (COUNT(parent.category_name) - (sub_tree.depth + 1)) AS depth
            FROM categories AS node, categories AS parent, categories AS sub_parent,
                (SELECT node.category_name, (COUNT(parent.category_name) - 1) AS depth
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
   
        return $catagoryAndChildren;
    }
}
