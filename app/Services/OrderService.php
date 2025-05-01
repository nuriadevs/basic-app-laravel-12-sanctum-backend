<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Validations\OrderValidation;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;


/**
 * Class OrderService
 * 
 * Service class for managing orders.
 *
 * @package App\Services
 */
class OrderService
{
    /**
     * Create a new order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function createOrder(Request $request)
    {
        // Validate the request data
        OrderValidation::validateOrderCreation($request->all());

        $orderDetails = [];
        $totalAmount = 0;

        // foreach loop to iterate over the order details
        foreach ($request->order_details as $detail) {
            $product = Product::findOrFail($detail['product_id']);

            $orderDetails[] = [
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'price' => $product->price,
                'total_amount' => $detail['quantity'] * $product->price,
                
            ];

            $totalAmount += $detail['quantity'] * $product->price;
        }

        // Create the order with the user ID and total amount
        $order = Order::create([
            'user_id' => $request->user_id,
            'total_amount' => $totalAmount,
        ]);

        foreach ($orderDetails as $detail) {
            $order->orderDetails()->create($detail);
        }

        $order->load(['orderDetails.product' => function ($query) {
            $query->select('id', 'price');
        }]);

        // Format the order details for the response
        $orderDetails = $order->orderDetails->map(function ($detail) {
            return [
                'id' => $detail->id,
                'order_id' => $detail->order_id,
                'product_id' => $detail->product_id,
                'total_amount' => number_format($detail->total_amount, 2, '.', ''),
                'quantity' => $detail->quantity,
                'price' => number_format($detail->product->price, 2, '.', ''),
                'created_at' => $detail->created_at,
                'updated_at' => $detail->updated_at,
            ];
        });

        return [
            'user_id' => $order->user_id,
            'total_amount' => number_format($order->total_amount, 2, '.', ''),
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'id' => $order->id,
            'order_details' => [
                'products' => $orderDetails,
            ],
        ];
    }

    /**
     * Update an existing order.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *  
     */
    public static function updateOrder(Request $request, $id)
    {
        // Validate the request data
        OrderValidation::validateOrderUpdate($request->all());
    
        // Find the order by ID
        $order = Order::findOrFail($id);
    
        $orderDetails = [];
        $totalAmount = 0;
    
        // Get the IDs of the details sent in the request
        $requestedDetailIds = collect($request->order_details)->pluck('id')->filter()->toArray();
    
        // Delete order details that are not in the request
        $order->orderDetails()->whereNotIn('id', $requestedDetailIds)->delete();
    
        // foreach loop to iterate over the order details
        foreach ($request->order_details as $detail) {
            $product = Product::findOrFail($detail['product_id']);
    
            if (!empty($detail['id'])) {
                $orderDetail = $order->orderDetails()->where('id', $detail['id'])->first();
                if ($orderDetail) {
                    $orderDetail->update([
                        'product_id' => $detail['product_id'],
                        'quantity' => $detail['quantity'],
                        'price' => $product->price,  
                        'total_amount' => $detail['quantity'] * $product->price,
                    ]);
                }
            } else {
               
                $orderDetail = $order->orderDetails()->create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $product->price,
                    'total_amount' => $detail['quantity'] * $product->price,
                ]);
            }
    
            $orderDetails[] = [
                'id' => $orderDetail->id,
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $orderDetail->quantity,
                'total_amount' => number_format($orderDetail->total_amount, 2, '.', ''),
                'price' => number_format($product->price, 2, '.', ''),  
                'created_at' => $orderDetail->created_at,
                'updated_at' => $orderDetail->updated_at,
            ];
    
            $totalAmount += $orderDetail->total_amount;
        }
    
        $order->update([
            'user_id' => $request->user_id,
            'total_amount' => $totalAmount,
        ]);
    
        return [
            'user_id' => $order->user_id,
            'total_amount' => number_format($order->total_amount, 2, '.', ''),
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'id' => $order->id,
            'order_details' => [
                'products' => $orderDetails,
            ],
        ];
    }
    

    /**
     * Get all orders.
     * @return array
     */
    public static function getAllOrders()
    {
        $orders = Order::with(['orderDetails.product' => function ($query) {
            $query->select('id', 'price');
        }])->get();

        return $orders->map(function ($order) {
            return [
                'user_id' => $order->user_id,
                'total_amount' => number_format($order->total_amount, 2, '.', ''),
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'id' => $order->id,
                'order_details' => [
                    'products' => $order->orderDetails->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'order_id' => $detail->order_id,
                            'product_id' => $detail->product_id,
                            'quantity' => $detail->quantity,
                            'total_amount' => number_format($detail->total_amount, 2, '.', ''),
                            'price' => number_format($detail->product->price, 2, '.', ''),
                            'created_at' => $detail->created_at,
                            'updated_at' => $detail->updated_at,
                        ];
                    }),
                ],
            ];
        });
    }

    /**
     * Get a specific order by ID.
     * @return array
     */
    public static function getOrderById($id)
    {
        $order = Order::with(['orderDetails.product' => function ($query) {
            $query->select('id', 'price');
        }])->findOrFail($id);

        return [
            'user_id' => $order->user_id,
            'total_amount' => number_format($order->total_amount, 2, '.', ''),
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'id' => $order->id,
            'order_details' => [
                'products' => $order->orderDetails->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'order_id' => $detail->order_id,
                        'product_id' => $detail->product_id,
                        'quantity' => $detail->quantity,
                        'total_amount' => number_format($detail->total_amount, 2, '.', ''),
                        'price' => number_format($detail->product->price, 2, '.', ''),
                        'created_at' => $detail->created_at,
                        'updated_at' => $detail->updated_at,
                    ];
                }),
            ],
        ];
    }
}