<?php

namespace App\Helpers;

use CodeIgniter\HTTP\Response;

class ResponseFormatter
{

    protected static $response = [
        "meta" => [
            "code" => 200,
            "status" => "success",
            "message" => null
        ],
        "data" => null
    ];

    public static function success($data = null, $message = null): Response
    {
        self::$response["meta"]["message"] = $message;
        self::$response["data"] = $data;

        return response()->setJSON(self::$response, self::$response["meta"]["code"]);
    }

    public static function error($data = null, $message = null, $code = 400): Response
    {
        self::$response = [
            "meta" => [
                "code" => $code,
                "status" => "error",
                "message" => $message
            ],
            "data" => $data
        ];

        return response()->setJSON(self::$response, $code);
    }

}