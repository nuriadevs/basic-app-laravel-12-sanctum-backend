<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
use App\Models\User;
use App\Validations\UserValidation;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Class UserController
 *
 * Controller for managing user resources.
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Displays all users.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        try {
            $users = User::all();
            return ApiResponse::success('List of users', 200, $users);
        } catch (Exception $e) {
            return ApiResponse::error('Error while retrieving the list of users ' . $e->getMessage(), 500);
        }
    }

    /**
     * Displays a specific user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return ApiResponse::success('User', 200, $user);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('User not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error getting the user ' . $e->getMessage(), 500);
        }
    }


    /**
     * Stores a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try {

            UserValidation::validateUserUpdate($request->input());

            $user = User::create($request->all());
            return ApiResponse::success('User created successfully', 200, $user);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return ApiResponse::error('Validation errors: ' . implode(', ', $errors), 422);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('User not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error getting the user ' . $e->getMessage(), 500);
        }
    }




    /**
     * Updates an existing user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        try {

            UserValidation::validateUserUpdate($request->input());

            $user = User::findOrFail($id);
            $user->update($request->all());
            return ApiResponse::success('User updated successfully', 200, $user);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return ApiResponse::error('Validation errors: ' . implode(', ', $errors), 422);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('User not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error updating the user ' . $e->getMessage(), 500);
        }
    }


    /**
     * Deletes an existing user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return ApiResponse::success('User deleted successfully', 200, $user);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('User not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error deleting the user ' . $e->getMessage(), 500);
        }
    }
}
