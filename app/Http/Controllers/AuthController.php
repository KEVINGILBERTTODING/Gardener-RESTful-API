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
        $apiKeyHeader = $request->header('API-KEY');
        if ($apiKeyHeader == env('API_KEY')) {
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
                        'message' => 'Yahh terjadi kesalahan'
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
        } else {
            return response([
                'status' => 'failed',
                'message' => 'Invalid Api Key'
            ], 401);
        }
    }

    function login(Request $request)
    {
        $apiKeyHeader = $request->header('API-KEY');
        if ($apiKeyHeader == env('API_KEY')) {
            $email = $request->input('email');
            $password = $request->input('password');

            $validateEmail = User::where('email', $email)->first();
            if ($validateEmail != null) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required | email |unique:users,email',
                    'password' => 'required | min:8'
                ]);

                $cekPassword = Hash::check($password, $validateEmail['password']);

                if ($cekPassword) {
                    return response([
                        'status' => 'success',
                        'message' => 'Login success',
                        'data' => [
                            'id' => $validateEmail['id'],
                            'name' => $validateEmail['name'],
                            'email' => $validateEmail['email']
                        ]
                    ], 200);
                } else {
                    return response([
                        'status' => 'failed',
                        'message' => 'Yah password kamu salah nih'
                    ], 401);
                }
            } else {
                return response([
                    'status' => 'failed',
                    'message' => 'Yah email belum terdaftar nihh'
                ], 404);
            }
        } else {
            return response([
                'status' => 'failed',
                'message' => 'Invalid Api Key'
            ], 401);
        }
    }
}
