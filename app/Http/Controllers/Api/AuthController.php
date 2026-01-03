<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    private const SUCCESS_REGISTERED = "Пользователь успешно зарегистрирован";
    private const ERROR_REGISTERED = "Пользователь не зарегистрирован";
    private const SUCCESS_LOGIN = "Вход пользователя успешно осуществлен";
    private const ERROR_LOGIN = "Введены не верные данные";
    private const SUCCESS_LOGOUT = "Выход пользователя успешно осуществлен";
    private const ERROR_VALIDATED = "Ошибка валидации";

    public function signup(SignupRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"]),
            ]);

            if (!$user) {
                return $this->sendError(
                    self::ERROR_REGISTERED,
                    null,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                );
            }

            // Генерация токена с указанием способностей
            $token = $user->createToken("auth_token", ["*"])->plainTextToken;

            return $this->sendCreated(
                [
                    "user" => new UserResource($user),
                    "token" => $token,
                    "token_type" => "Bearer",
                    "expires_in" => config("sanctum.expiration"),
                ],
                self::SUCCESS_REGISTERED,
            );
        } catch (\Exception $e) {
            Log::error(self::ERROR_REGISTERED . ": " . $e->getMessage());

            return $this->sendError(
                self::ERROR_REGISTERED,
                config("app.debug") ? $e->getMessage() : null,
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return $this->sendError(
                self::ERROR_LOGIN,
                null,
                Response::HTTP_UNAUTHORIZED,
            );
        }
        /** $var User $user */
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken("auth_token", ["*"])->plainTextToken;

        return $this->sendResponse(
            [
                "user" => new UserResource($user),
                "token" => $token,
                "token_type" => "Bearer",
                "expires_in" => config("sanctum.expiration"),
            ],
            self::SUCCESS_LOGIN,
        );
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }
        return $this->sendNoContent(self::SUCCESS_LOGOUT);
    }
}
