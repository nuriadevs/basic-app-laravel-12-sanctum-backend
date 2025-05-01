<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Validations\OrderValidation;
use App\Http\Response\ApiResponse;
use App\Services\OrderService; // Importamos el servicio
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Class OrderController
 * 
 * Controller for managing orders.
 *
 * @package App\Http\Controllers
 */

class OrderController extends Controller
{

    /**
     * Displays all orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $orders = OrderService::getAllOrders();
            return ApiResponse::success('List of orders', 200, $orders);
        } catch (Exception $e) {
            return ApiResponse::error('Error while retrieving the list of orders ' . $e->getMessage(), 500);
        }
    }

    /**
     * Displays a specific order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $order = OrderService::getOrderById($id);

            return response()->json([
                'message' => 'Order retrieved successfully',
                'statusCode' => 200,
                'error' => false,
                'data' => $order,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Order not found',
                'statusCode' => 404,
                'error' => true,
                'data' => [],
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving the order',
                'statusCode' => 500,
                'error' => true,
                'data' => [],
            ], 500);
        }
    }


    /**
     * Stores a new order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            OrderValidation::validateOrderCreation($request->all());

            $orderData = OrderService::createOrder($request);

            return response()->json([
                'message' => 'Order created successfully',
                'statusCode' => 200,
                'error' => false,
                'data' => $orderData,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'statusCode' => 422,
                'error' => true,
                'data' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error creating the order',
                'statusCode' => 500,
                'error' => true,
                'exception' => $e->getMessage(), // <--- AÑADIR ESTO
                'trace' => $e->getTraceAsString(), // <--- Y ESTO TAMBIÉN
                'data' => [],
            ], 500);
        }
    }

    /**
     * Updates an existing order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $orderData = OrderService::updateOrder($request, $id);

            return ApiResponse::success('Order updated successfully', 200, $orderData);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Order or product not found ' . $e->getMessage(), 404);
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed', 422, $e->validator->errors());
        } catch (Exception $e) {
            return ApiResponse::error('Error updating the order ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deletes an existing order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            $order->orderDetails()->delete();

            $order->delete();

            return ApiResponse::success('Order deleted successfully', 200, $order);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Order not found ' . $e->getMessage(), 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error deleting the order ' . $e->getMessage(), 500);
        }
    }
}
