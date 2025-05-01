<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\OrderDetail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Test para la gestión de pedidos
 * Nombre de todos los tests de la clase: OrderManagementTest:
 * - user_create_an_order: Test para crear un pedido.
 * - create_order_missing: Test para crear un pedido sin los campos obligatorios.
 */
class OrderManagementTest extends TestCase
{
    use RefreshDatabase;



    /**
     * Test para visualizar todos los pedidos.
     */
    #[Test]
    public function user_view_all_orders()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'total_amount',
                    'order_details' => [
                        '*' => [
                            'id',
                            'product_id',
                            'quantity',
                            'price',
                            'total_amount',
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Test para crear un pedido.
     */
    #[Test]
    public function user_create_an_order()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $product = Product::factory()->create([
            'price' => 100.00,
        ]);

        $payload = [
            'user_id' => $user->id,
            'total_amount' => 100.00,
            'order_details' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 100.00,
                    'total_amount' => 100.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(200);
    }

    /**
     * Test para crear un pedido sin los campos obligatorios.
     */
    #[Test]
    public function create_order_missing()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/orders', [
            // intentionally missing 'user_id' and 'total_amount'
            'order_details' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50.00,
                    'total_amount' => 100.00,
                ],
            ],
        ]);

        $response->assertStatus(422);

    }

    /**
     * Test para visualizar un pedido específico.
     */
    #[Test]
    public function user_view_a_order()
    {
        Sanctum::actingAs(User::factory()->create());

        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
    }

    /**
     * Test para eliminar un pedido.
     */
    #[Test]
    public function user_delete_a_order()
    {
        Sanctum::actingAs(User::factory()->create());

        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }


    /**
     * Test para actualizar un pedido.
     */
    #[Test]
    public function user_update_a_order()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $order = Order::factory()->create([
            'id' => 1,
            'user_id' => $user->id,
            'total_amount' => 100.00,
        ]);

        $product = Product::factory()->create([
            'price' => 50.00,
        ]);

        // Creamos un detalle de orden vinculado al pedido
        $payload = [
            'user_id' => $user->id,
            'total_amount' => 50.00,
            'order_details' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => 50.00,
                    'total_amount' => 50.00,
                ],
            ],
        ];
        

        // Ahora actualizamos la orden incluyendo el ID del detalle
        $response = $this->putJson("/api/orders/{$order->id}", $payload);

        $response->dump();

        $response->assertStatus(200);

    }
}
