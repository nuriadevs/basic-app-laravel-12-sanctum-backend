<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Validations\ProductValidation;
use App\Http\Response\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Class ProductController
 *
 * Controller for managing product resources.
 *
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * Displays all products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $products = Product::all();
            return ApiResponse::success('List of products', 200, $products);
        } catch (Exception $e) {
            return ApiResponse::error('Error while retrieving the list of products ' . $e->getMessage(), 500);
        }
    }

    /**
     * Displays a specific product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return ApiResponse::success('Product', 200, $product);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Product not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error while retrieving the product ' . $e->getMessage(), 500);
        }
    }


    /**
     * Stores a new product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try {

            ProductValidation::validateProductCreation($request->input());

            $product = Product::create($request->all());
            return ApiResponse::success('Product created successfully', 201, $product);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Product not found ' . $e->getMessage(), 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed', 422, $e->validator->errors());
        } catch (Exception $e) {
            return ApiResponse::error('Error creating the product ' . $e->getMessage(), 500);
        }
    }

    /**
     * Updates an existing product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        try {

            ProductValidation::validateProductCreation($request->input());

            $product = Product::findOrFail($id);
            $product->update($request->all());
            return ApiResponse::success('Product updated successfully', 200, $product);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return ApiResponse::error('Validation errors: ' . implode(', ', $errors), 422);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Product not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error updating the product ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deletes an existing product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return ApiResponse::success('Product deleted successfully', 200, $product);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Product not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error deleting the product ' . $e->getMessage(), 500);
        }
    }
}
