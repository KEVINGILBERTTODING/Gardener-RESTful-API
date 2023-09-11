<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    function getAllProduct()
    {
        $dataProduct = Product::get();
        return $dataProduct;
    }
}
