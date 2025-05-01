<?php

namespace App\Validations;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class LoginValidation
 *
 * Provides methods for validating user login data.
 *
 * @package App\Validations
 */
class LoginValidation
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }


    /**
     * Validates the user's login data.
     *
     * @param array $data The data to validate.
     * @return \Illuminate\Contracts\Validation\Validator The Laravel validator.
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */

    public static function validateUserLogin($data)
    {
        $rules = [
            'email' => [
                'required',
                'email',
                'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/',

            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^[a-zA-Z0-9]{12}$/',
            ],
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator;
    }
}
