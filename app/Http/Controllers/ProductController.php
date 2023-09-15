<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    function getAllProduct(Request $request)
    {
        $apiKeyHeader = $request->header('API-KEY');
        if ($apiKeyHeader == env('API_KEY')) {
            $dataProduct = Product::get();
            return response([
                'status' => 'success',
                'message' => 'Berhasil memuat data produk',
                'data' => $dataProduct
            ], 200);
        } else {
            return response([
                'status' => 'failed',
                'message' => 'Invalid api key',
            ], 400);
        }
    }

    function insert(Request $request)
    {
        $apiKeyHeader = $request->header('API-KEY');
        if ($apiKeyHeader == env('API_KEY')) {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:150',
                'price' => 'required|integer',
                'description' => 'string',
                'image' => 'required|image|mimes:png,jpg,jpeg|max:2048'
            ]);


            if ($validator->fails()) {
                return response([
                    'status' => 'failed',
                    'message' => 'Something went wrong',
                ], 400);
            } else {

                if ($request->hasFile('image')) {
                    $productImage = $request->file('image');
                    $uuid = Str::uuid();
                    $fileName = $uuid . '.' . $productImage->getClientOriginalExtension();
                    $productImage->storeAs('public/data/image/products/', $fileName);
                    $dataProduct = [
                        'product_name' => $request->input('product_name'),
                        'price' => $request->input('price'),
                        'image' => $fileName,
                        'description' => $request->input('description'),
                        'created_at' => now()
                    ];

                    $insertProduct = Product::insert($dataProduct);
                    if ($insertProduct) {
                        return response([
                            'status' => 'success',
                            'message' => 'Hore berhasil menambahkan product'
                        ], 200);
                    } else {
                        return response([
                            'status' => 'failed',
                            'message' => 'Yahh gagal menambahkan product'
                        ], 400);
                    }
                } else {
                    return response([
                        'status' => 'failed',
                        'message' => 'Yahh gagal menambahkan product'
                    ], 400);
                }
            }
        } else {
            return response([
                'status' => 'failed',
                'message' => 'API Key salah'
            ], 404);
        }
    }

    function update(Request $request)
    {
        $apiKeyHeader = $request->header('API-KEY');
        $productId = $request->input('product_id');
        if ($apiKeyHeader == env('API_KEY')) {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required | integer',
                'product_name' => 'required|string|max:150',
                'price' => 'required|integer',
                'description' => 'string'
            ]);


            if ($validator->fails()) {
                return response([
                    'status' => 'failed',
                    'message' => 'Something went wrong',
                ], 400);
            } else {

                // validasi jika update product tanpa image
                if ($request->input('is_image') == 1) {
                    $validatorImg = Validator::make($request->all(), [
                        'image' => 'required|image|mimes:png,jpg,jpeg|max:2048'
                    ]);
                    if ($validatorImg->fails()) {
                        return response([
                            'status' => 'failed',
                            'message' => 'Format gambar salah'
                        ], 401);
                    }

                    if ($request->hasFile('image')) {
                        $productImage = $request->file('image');
                        $uuid = Str::uuid();
                        $fileName = $uuid . '.' . $productImage->getClientOriginalExtension();
                        $productImage->storeAs('public/data/image/products/', $fileName);
                        $dataProduct = [
                            'product_name' => $request->input('product_name'),
                            'price' => $request->input('price'),
                            'image' => $fileName,
                            'description' => $request->input('description'),
                            'updated_at' => now()
                        ];

                        $updatePrdoduct = Product::where('id', $productId)->update($dataProduct);
                        if ($updatePrdoduct) {
                            return response([
                                'status' => 'success',
                                'message' => 'Hore berhasil mengubah product'
                            ], 200);
                        } else {
                            return response([
                                'status' => 'failed',
                                'message' => 'Yahh gagal mengubah product'
                            ], 400);
                        }
                    } else {
                        return response([
                            'status' => 'failed',
                            'message' => 'Yahh gagal mengubah product'
                        ], 400);
                    }
                } else {
                    $dataProduct = [
                        'product_name' => $request->input('product_name'),
                        'price' => $request->input('price'),
                        'description' => $request->input('description'),
                        'updated_at' => now()
                    ];

                    $updatePrdoduct = Product::where('id', $productId)->update($dataProduct);
                    if ($updatePrdoduct) {
                        return response([
                            'status' => 'success',
                            'message' => 'Hore berhasil mengubah product'
                        ], 200);
                    } else {
                        return response([
                            'status' => 'failed',
                            'message' => 'Yahh gagal mengubah product'
                        ], 400);
                    }
                }
            }
        } else {
            return response([
                'status' => 'failed',
                'message' => 'API Key salah'
            ], 404);
        }
    }

    function delete(Request $request)
    {
        $apiKeyHeader = $request->header('API-KEY');
        if ($apiKeyHeader == env('API_KEY')) {

            $validator  = Validator::make($request->all(), [
                'id' => 'required | integer'
            ]);
            if ($validator->fails()) {
                return response([
                    'status' => 'failed',
                    'message' => 'Yahh Terjadi kesalahan'
                ], 402);
            } else {

                $id = $request->input('id');
                $delete = Product::where('id', $id)->delete();
                if ($delete) {
                    return response([
                        'status' => 'success',
                        'message' => 'Horee berhasil menghapus produk'
                    ]);
                } else {
                    return response([
                        'status' => 'failed',
                        'message' => 'Yahh Gagal menghapus produk'
                    ], 402);
                }
            }
        } else {
            return response([
                'status' => 'failed',
                'message' => 'Invalid API Key'
            ], 402);
        }
    }

    function filter(Request $request)
    {
        $productName = $request->input('query');
        $apiKeyHeader = $request->header('API-KEY');
        if ($apiKeyHeader == env("API_KEY")) {
            $validator = Validator::make($request->all(), [
                'query' => 'required | string'
            ]);

            if ($validator->fails()) {
                return response([
                    'status' => 'failed',
                    'message' => 'Terjadi kesalahan'
                ], 404);
            } else {
                $dataProduct = Product::where('product_name', 'like', '%' . $productName . '%')
                    ->get();



                return response([
                    'status' => 'success',
                    'message' => 'Berhasil mengambil data product',
                    'data' => $dataProduct
                ], 200);
            }
        } else {
            return response([
                'status' => 'failed',
                'message' => 'Api Key invalid'
            ], 404);
        }
    }
}
