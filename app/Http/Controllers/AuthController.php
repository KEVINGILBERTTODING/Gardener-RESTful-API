<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    function register(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user == null) {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required | string | max:255 |min:8',
                    'email' => 'required | email | unique:users,email',
                    'password' => 'required | string | min:8'
                ]
            );

            if ($validator->fails()) {
                return response([
                    'status' => 'failed',
                    'message' => 'Yahh terjadi kesalan'
                ], 401);
            }

            $dataUser = [
                'name' => $request->input('name'),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'created_at' => now()
            ];

            $insert = User::insert($dataUser);
            if ($insert) {
                return response([
                    'status' => 'success',
                    'message' => 'Yeay kamu berhasil registrasi'
                ], 200);
            } else {
                return response([
                    'status' => 'failed',
                    'message' => 'Yah kamu gagal registrasi'
                ], 401);
            }
        } else {
            return response([
                'status' => 'failed',
                'message' => 'Yah email telah terdaftar'
            ], 401);
        }
    }
}
