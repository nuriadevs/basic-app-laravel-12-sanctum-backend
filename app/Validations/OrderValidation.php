<?php

namespace App\Validations;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * Class OrderValidation
 *
 * Provides methods for validating order creation data.
 *
 * @package App\Validations
 */
class OrderValidation
{

    /**
     * Validates the creation data of an order.
     *
     * @param array $data The data to validate.
     * @return \Illuminate\Contracts\Validation\Validator The Laravel validator.
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */

    public static function validateOrderCreation($data): void
    {
        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'order_details' => ['required', 'array', 'min:1'],
            'order_details.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'order_details.*.quantity' => ['required', 'integer', 'min:1'],
            'order_details.*.total_amount' => ['required_if:order_details,true', 'numeric', 'min:0'],
        ];

        try {
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception('Unexpected validation error: ' . $e->getMessage(), 500);
        }
    }



    public static function validateOrderUpdate($data): void
    {



        $rules = [
            'total_amount' => ['sometimes', 'numeric', 'min:0'],
            'order_details' => ['sometimes', 'array', 'min:1'],
            'order_details.*.product_id' => ['required_with:order_details', 'integer', 'exists:products,id'],
            'order_details.*.total_amount' => [], 
        ];


        try {
            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        } catch (ValidationException $e) {
             
            throw new ValidationException($e->validator, $e->getMessage(), $e->status);
        } catch (Exception $e) {
            throw new Exception('Unexpected validation error: ' . $e->getMessage(), 500);
        }

    }
}
