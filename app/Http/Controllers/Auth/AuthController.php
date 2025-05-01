<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Validations\LoginValidation;
use App\Validations\UserValidation;
use App\Http\Response\ApiResponse;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;



/**
 * Class AuthController
 *
 * Controller for managing user authentication.
 *
 * @package App\Http\Controllers\Auth
 */

class AuthController extends Controller
{

    /**
     * Log in a user.
     *
     * This method validates the login credentials provided by the user
     * and generates an access token upon successful authentication.
     *
     * @param  \Illuminate\Http\Request  $request The HTTP request object containing the user's login data.
     * @return \Illuminate\Http\Response The HTTP response containing the result of the login attempt.
     */
    public function login(Request $request)
    {

        try {

            LoginValidation::validateUserLogin($request->input());

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ApiResponse::error('This user does not exist', 404, $user);
            }

            if (!Hash::check($request->password, $user->password)) {
                return ApiResponse::error('Incorrect credentials', 401, $user);
            }

            if (!$user->is_active) {
                return ApiResponse::error('This account is inactive', 401);
            }

            $token = $user->createToken($request->email)->plainTextToken;

            return ApiResponse::success('User logged in successfully', 200, [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return ApiResponse::error('Validation errors: ' . implode(', ', $errors), 422);
        } catch (Exception $e) {
            return ApiResponse::error('Error while performing the login ' . $e->getMessage(), 500);
        }
    }


    /**
     * Register a new user.
     *
     * This method validates the provided data to create a new user
     * and, if valid, creates the user and generates an access token for them.
     *
     * @param  \Illuminate\Http\Request  $request The HTTP request object containing the new user's data.
     * @return \Illuminate\Http\Response The HTTP response containing the result of the registration attempt.
     */
    public function register(Request $request)
    {
        try {
            UserValidation::validateUserCreation($request->all());

            $user = User::create($request->all());

            $token = $user->createToken('API TOKEN')->plainTextToken;

            return ApiResponse::register('User registered in successfully', 201, [
                'user' => $user,
                'token' => $token,
            ]);
        } catch (QueryException $e) {
            if (strpos($e->getMessage(), 'users_dni_unique') !== false) {
                return ApiResponse::error('The dni is already registered', 500);
            }
            return ApiResponse::error('Database error: ' . $e->getMessage(), 500);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return ApiResponse::error('Validation errors: ' . implode(', ', $errors), 422);
        } catch (Exception $e) {
            return ApiResponse::error('Error creating the user ' . $e->getMessage(), 500);
        }
    }

    /**
     * Logout an authenticated user.
     *
     * This method verifies if the user is authenticated with a valid token, to delete it and logout.
     *
     * @return \Illuminate\Http\Response The HTTP response containing the result of the logout attempt.
     */

    public function logout(Request $request)
    {

        try {
            $user = $request->user();
            $user->tokens()->delete();
            return ApiResponse::success('User successfully disconnected!', 200);
        } catch (Exception $e) {
            return ApiResponse::error('Error disconnecting the user ' . $e->getMessage(), 500);
        }
    }
}
