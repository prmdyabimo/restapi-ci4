<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\ResponseFormatter;
use App\Models\ModelUsers;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Exception;

class AuthController extends BaseController
{
    use ResponseTrait;

    private ModelUsers $modelUsers;

    public function __construct()
    {
        $this->modelUsers = new ModelUsers();
    }

    public function doLogin()
    {
        try {
            $data = $this->request->getPost();

            $validationRules = [
                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Email wajib diisi',
                        'valid_email' => 'Email tidak valid'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Password wajib diisi'
                    ]
                ]
            ];

            if (!$this->validate($validationRules)) {
                return ResponseFormatter::error(
                    $this->validator->getErrors(),
                    'Data yang anda masukkan tidak sesuai',
                    400
                );
            }

            $user = $this->modelUsers->where('email', $data['email'])->first();
            $passwordVerify = password_verify($data['password'], $user['password']);

            if (!$user || !$passwordVerify) {
                return ResponseFormatter::error(
                    null,
                    'Email dan password salah',
                    400
                );
            }

            // JWT CONFIGURATION
            $key = getenv('JWT_SECRET_KEY');
            $iat = time();
            $exp = getenv('JWT_TIME_TO_LIVE') + $iat;

            $payload = [
                "iss" => "Issue of the JWT",
                "aud" => "Audience that the JWT",
                "sub" => "Subject of the JWT",
                "iat" => $iat, // Time the JWT issued at
                "exp" => $exp, // Expiration time of token
                "email" => $user['email'],
            ];

            $token = JWT::encode($payload, $key, 'HS256');

            $response = [
                'token' => $token,
                'user' => $user
            ];

            return ResponseFormatter::success(
                $response,
                'Login berhasil'
            );
        } catch (Exception $exception) {
            return ResponseFormatter::error(
                null,
                $exception->getMessage(),
                500
            );
        }
    }
}
