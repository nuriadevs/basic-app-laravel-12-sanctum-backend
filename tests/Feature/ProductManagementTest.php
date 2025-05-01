<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;


/**
 * Test para productos
 * 
 * Nombre de todos los tests de la clase: ProductManagementTest:
 * - user_create_a_product: Test para crear un producto.
 * - user_view_a_product: Test para visualizar un producto.
 * - user_update_a_product: Test para actualizar un producto.
 * - user_delete_a_product: Test para eliminar un producto.
 * - user_create_a_product_without_name: Test para crear un producto sin nombre.
 */
class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test para crear un producto.
     */
    #[Test]
    public function user_create_a_product(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'created_at',
                'updated_at',
            ]
        ]);

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    /**
     * Test para visualizar un producto.
     */
    #[Test]
    public function user_view_a_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $product->name,
        ]);
    }

    /**
     *  Test para actualizar un producto.
     */
    #[Test]
    public function user_update_a_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $response = $this->patchJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated description', 
            'price' => 150.00, 
            'stock' => 20, 
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
        
    }

    /**
     * Test para eleminar un producto.
     */
    #[Test]
    public function user_delete_a_product(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /**
     * Test para crear un producto sin nombre
    */
    #[Test]
    public function user_create_a_product_without_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/products', [
         
            'description' => 'Test product.',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data' => [
                'name',
            ]
        ]);
    }

}
