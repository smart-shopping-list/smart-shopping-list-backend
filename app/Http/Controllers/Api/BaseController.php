<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends Controller
{
    /**
     * Успешный ответ
     */
    public function sendResponse(
        mixed $data,
        string $message,
        int $code = Response::HTTP_OK,
    ): JsonResponse {
        $response = [
            "success" => true,
            "message" => $message,
        ];

        if (
            $data instanceof JsonResource ||
            $data instanceof ResourceCollection
        ) {
            $response = array_merge(
                $response,
                $data->response()->getData(true),
            );
        } else {
            $response["data"] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Ответ с ошибкой
     */
    public function sendError(
        string $error,
        mixed $errors = null,
        int $code = Response::HTTP_BAD_REQUEST,
    ): JsonResponse {
        $response = [
            "success" => false,
            "message" => $error,
        ];

        if ($errors !== null) {
            $response["errors"] = $errors;
        }
        return response()->json($response, $code);
    }

    /**
     * Успешное создание ресурса
     */
    public function sendCreated(
        mixed $data,
        string $message = "Resource created successfully",
    ): JsonResponse {
        return $this->sendResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Ответ без содержимого
     */
    public function sendNoContent(string $message = ""): JsonResponse
    {
        return $this->sendResponse([], $message, Response::HTTP_NO_CONTENT);
    }

    /**
     * Ответ для пагинированных данных
     */
    public function sendPaginatedResponse(
        ResourceCollection $collection,
        string $message = "",
    ): JsonResponse {
        return $this->sendResponse($collection, $message);
    }
}
