<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Hash;

/**
 * Test para verificar la autenticación de usuarios.
 */

/** Nombre de los test:user_register, user_register_with_invalid_password, user_register_with_existing_email, user_register_with_invalid_dni, user_login, user_login_with_inactive_account
* 
* Descripción de los test: 
* user_register: Verifica el registro de un usuario, 
* user_register_with_invalid_password: Verifica el registro de un usuario con contraseña inválida, 
* user_register_with_existing_email: Verifica el registro de un usuario con email existente, 
* user_register_with_invalid_dni: Verifica el registro de un usuario con dni inválido, 
* user_login: Verifica el login de un usuario, 
* user_login_with_inactive_account: Verifica el login de un usuario con cuenta inactiva
*/

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test para verificar la creación de un usuario.
     * 
     * @return array
     */
    private function getValidUserData(): array
    {
        return [
            'name' => 'Test',
            'first_surname' => 'User',
            'second_surname' => 'Example',
            'email' => 'test@example.com',
            'dni' => '12345678A',
            'postal_code' => '29001',
            'city' => 'Málaga',
            'address' => 'Calle Nueva 123',
            'phone_number' => '000000000',
            'birthdate' => '1980-01-01',
            'profile_picture' => null,
            'role' => 'customer',
            'is_active' => true,
            'password' => 'password1234'
        ];
    }

    /**
     * Test para verificar el registro de un usuario.
     * 
     * @return void
     */
    #[Test]
    public function user_register(): void
    {
        $userData = $this->getValidUserData();

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201);

        // Verificar estructura de respuesta
        $response->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data' => [
                'user',
                'token',
            ]
        ]);


        $response->assertJson([
            'statusCode' => 201,
            'error' => false,
        ]);

        // Verificar que el usuario fue creado en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
            'dni' => $userData['dni']
        ]);

        // Verificar que la contraseña está hasheada
        $user = User::where('email', $userData['email'])->first();

        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    /**
     * Test para verificar el registro de un usuario con contraseña inváida.
     * 
     * @return void
     */
    #[Test]
    public function user_register_with_invalid_password(): void
    {
        $userData = $this->getValidUserData();
        $userData['password'] = 'hola'; 

        $response = $this->postJson('/api/auth/register', $userData);

        // Verificar error de validación
        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Validation errors: The password field must be at least 8 characters.'
        ]);

        // Verificar que el usuario NO fue creado
        $this->assertDatabaseMissing('users', ['email' => $userData['email']]);
    }

    /**
     * Test para verificar el registro de un usuario con email existente.
     * 
     * @return void
     */
    #[Test]
    public function user_register_with_existing_email(): void
    {
        // Crear usuario inicial
        User::factory()->create(['email' => 'test@example.com']);

        // Intentar registrar otro usuario con el mismo email
        $userData = $this->getValidUserData();

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => true
        ]);

        // Verificar que el mensaje contiene información sobre email duplicado
        $responseContent = json_decode($response->getContent(), true);
        $this->assertStringContainsString('email', $responseContent['message']);
    }

    /**
     * Test para verificar el registro de un usuario con dni inválido.
     * 
     * @return void
     */
    #[Test]
    public function user_register_with_invalid_dni(): void
    {
        $userData = $this->getValidUserData();
        $userData['dni'] = 'invalid-dni';

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422);

        // Verificar que el usuario NO fue creado
        $this->assertDatabaseMissing('users', ['email' => $userData['email']]);
    }

    /**
     * Test para verificar el login de un usuario.
     * 
     * @return void
     */
    #[Test]
    public function user_login(): void
    {
        // Crear usuario con password 
        $password = 'password1234';
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt($password),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => $password,
        ]);


        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure([
            'message',
            'statusCode',
            'error',
            'data' => [
                'user',
                'token',
            ]
        ]);

        $loginResponse->assertJson([
            'error' => false,
            'statusCode' => 200,
        ]);

        // Verificar que el token existe y no está vacío
        $responseData = $loginResponse->json();
        $this->assertNotEmpty($responseData['data']['token']);
    }

    /**
     * Test para verificar el login de un usuario con email inactivo.
     * 
     * @return void
     */
    #[Test]
    public function user_login_with_inactive_account(): void
    {
        // Crear usuario inactivo
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password1234'),
            'is_active' => false
        ]);

        // Intentar login
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password1234',
        ]);

        $loginResponse->assertStatus(401);

        // Verificar que el error esté presente en la respuesta
        $loginResponse->assertJson([
            'error' => true,
            'message' => 'This account is inactive',
        ]);
    }


}
