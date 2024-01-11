<?php

namespace App\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\ModelEmployees;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class EmployeeController extends ResourceController
{
    private ModelEmployees $modelEmployees;

    public function __construct()
    {
        $this->modelEmployees = new ModelEmployees();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return ResponseInterface
     */
    public function index()
    {
        try {
            $employees = $this->modelEmployees->orderBy('created_at', 'DESC')->findAll();

            if (empty($employees)) {
                return ResponseFormatter::error(
                    null,
                    'Belum ada data karyawan',
                    400
                );
            }

            return ResponseFormatter::success(
                $employees,
                'Data karyawan berhasil didapatkan'
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
            $employee = $this->modelEmployees->find($id);

            if (!$employee) {
                return ResponseFormatter::error(
                    null,
                    'Data karyawan tidak ditemukan',
                    400
                );
            }

            return ResponseFormatter::success(
                $employee,
                'Data karyawan berhasil ditemukan'
            );
        } catch (Exception $exception) {
            return ResponseFormatter::error(
                null,
                $exception->getMessage(),
                400
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
            $data = $this->request->getPost();

            $validationRules = [
                'name' => [
                    'rules' => 'required|alpha_space',
                    'errors' => [
                        'required' => 'Name is required',
                        'alpha_space' => 'Name cannot contain numbers'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|is_unique[employees.email]',
                    'errors' => [
                        'required' => 'Email is required',
                        'valid_email' => 'Email is not valid',
                        'is_unique' => 'Email is already'
                    ]
                ]
            ];

            if (!$this->validate($validationRules)) {
                return ResponseFormatter::error(
                    $this->validator->getErrors(),
                    'Gagal menambahkan data karyawan',
                    400
                );
            }

            $this->modelEmployees->save($data);

            return ResponseFormatter::success(
                $data,
                'Data karyawan berhasil ditambahkan'
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
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        try {
            $employee = $this->modelEmployees->find($id);
            $data = $this->request->getRawInput();

            if (!$employee) {
                return ResponseFormatter::error(
                    null,
                    'Data karyawan tidak ditemukan',
                    400
                );
            }

            $validationRules = [
                'name' => [
                    'rules' => 'required|alpha_space',
                    'errors' => [
                        'required' => 'Name is required',
                        'alpha_space' => 'Name cannot contain numbers'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email',
                    'errors' => [
                        'required' => 'Email is required',
                        'valid_email' => 'Email is not valid'
                    ]
                ]
            ];

            if (!$this->validate($validationRules)) {
                return ResponseFormatter::error(
                    $this->validator->getErrors(),
                    'Gagal mengubah data karyawan',
                    400
                );
            }

            $this->modelEmployees->update($id, $data);

            return ResponseFormatter::success(
                $data,
                'Data karyawan berhasil diubah'
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
            $employee = $this->modelEmployees->find($id);

            if (!$employee) {
                return ResponseFormatter::error(
                    null,
                    'Data karyawan tidak ditemukan',
                    400
                );
            }

            $this->modelEmployees->delete($id);

            return ResponseFormatter::success(
                $employee,
                'Data karyawan berhasil dihapus'
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
