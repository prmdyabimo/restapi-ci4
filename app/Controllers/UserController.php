<?php

namespace App\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\ModelUsers;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class UserController extends ResourceController
{
    private ModelUsers $modelUsers;

    public function __construct()
    {
        $this->modelUsers = new ModelUsers();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return ResponseInterface
     */
    public function index()
    {
        try {
            $users = $this->modelUsers->orderBy('created_at', 'DESC')->findAll();

            if (empty($users)) {
                return ResponseFormatter::error(
                    null,
                    'Belum ada data pengguna',
                    400
                );
            }

            return ResponseFormatter::success(
                $users,
                'Data pengguna berhasil didapatkan'
            );

        } catch (Exception $exception) {
            return ResponseFormatter::error(
                null,
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Return the properties of a resource object
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        try {
            $user = $this->modelUsers->find($id);

            if (!$user) {
                return ResponseFormatter::error(
                    null,
                    'Data pengguna tidak ditemukan',
                    400
                );
            }

            return ResponseFormatter::success(
                $user,
                'Data pengguna berhasil ditemukan'
            );
        } catch (Exception $exception) {
            return ResponseFormatter::error(
                null,
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return ResponseInterface
     */
    public function create()
    {
        try {
            $data = [
                'email' => htmlspecialchars($this->request->getVar('email')),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];

            $validationRules = [
                'email' => [
                    'rules' => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'required' => 'Email wajib diisi',
                        'valid_email' => 'Email tidak valid',
                        'is_unique' => 'Email sudah terdaftar'
                    ]
                ],
                'password' => [
                    'rules' => 'required|regex_match[/^(?=.*[a-zA-Z])(?=.*\d).+$/]|min_length[8]',
                    'errors' => [
                        'required' => 'Password wajib diisi',
                        'regex_match' => 'Password harus mengandung gabungan huruf dan angka',
                        'min_length' => 'Password minimal 8 karakter'
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

            $this->modelUsers->save($data);

            return ResponseFormatter::success(
                $data,
                'Data pengguna berhasil ditambahkan'
            );
        } catch (Exception $exception) {
            return ResponseFormatter::error(
                null,
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        try {
            $user = $this->modelUsers->find($id);

            if (!$user) {
                return ResponseFormatter::error(
                    null,
                    'Data pengguna tidak ditemukan',
                    400
                );
            }

            return ResponseFormatter::success(
                $user,
                'Data pengguna berhasil ditemukan'
            );
        } catch (Exception $exception) {
            return ResponseFormatter::error(
                null,
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        try {
            // Mendapatkan seluruh data dari body request dalam bentuk string JSON
            $requestData = $this->request->getRawInput();

            // Mengakses nilai yang diinginkan
            $email = isset($requestData['email']) ? $requestData['email'] : null;
            $newPassword = isset($requestData['new_password']) ? $requestData['new_password'] : null;
            $oldPassword = isset($requestData['old_password']) ? $requestData['old_password'] : null;

            $user = $this->modelUsers->find($id);

            if (!$user) {
                return ResponseFormatter::error(
                    null,
                    'Data pengguna tidak ditemukan',
                    400
                );
            }

            $updatedData = [
                'updated_at' => Time::now()->toDateTimeString(),
            ];

            if (!empty($oldPassword) || !empty($newPassword)) {
                $validationRulesForPassword = [
                    'new_password' => [
                        'rules' => 'required|regex_match[/^(?=.*[a-zA-Z])(?=.*\d).+$/]|min_length[8]',
                        'errors' => [
                            'required' => 'Password wajib diisi',
                            'regex_match' => 'Password baru harus mengandung gabungan huruf dan angka',
                            'min_length' => 'Password baru minimal 8 karakter'
                        ]
                    ],
                    'old_password' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Password lama wajib diisi'
                        ]
                    ]
                ];

                if (!$this->validate($validationRulesForPassword)) {
                    return ResponseFormatter::error(
                        $this->validator->getErrors(),
                        'Data yang anda masukkan tidak sesuai',
                        400
                    );
                }

                $passwordVerify = password_verify($oldPassword, $user['password']);

                if (!$passwordVerify) {
                    return ResponseFormatter::error(
                        null,
                        'Password anda salah',
                        400
                    );
                }

                $updatedData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            } else {
                $validationRulesForEmail = [
                    'email' => [
                        'rules' => 'required|valid_email',
                        'errors' => [
                            'required' => 'Email wajib diisi',
                            'valid_email' => 'Email tidak valid'
                        ]
                    ],
                ];

                if (!$this->validate($validationRulesForEmail)) {
                    return ResponseFormatter::error(
                        $this->validator->getErrors(),
                        'Data yang anda masukkan tidak sesuai',
                        400
                    );
                }

                $updatedData['email'] = $email;
            }

            $this->modelUsers->update($id, $updatedData);

            return ResponseFormatter::success(
                $updatedData,
                'Data pengguna berhasil diperbarui'
            );

        } catch (Exception $exception) {
            return ResponseFormatter::error(
                null,
                $exception->getMessage(),
                500
            );
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        try {
            $user = $this->modelUsers->find($id);

            if (!$user) {
                return ResponseFormatter::error(
                    null,
                    'Data pengguna tidak ditemukan',
                    400
                );
            }

            $this->modelUsers->delete($id);

            return ResponseFormatter::success(
                $user,
                'Data pengguna berhasil dihapus'
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
