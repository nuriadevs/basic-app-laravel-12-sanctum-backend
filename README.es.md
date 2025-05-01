
# Aplicación Básica Laravel 12 Sanctum (Backend)

📘 Disponible también en [Inglés](./README.md)

Se actualizó la aplicación a Laravel 12 y se añadieron nuevas características, incluidos los tests.

Esta aplicación ha sido desarrollada con Laravel, ofrece operaciones CRUD y un sistema básico de autenticación y registro.

## Entorno 🛠️

* [PHP 8.2.4](https://www.php.net/releases/8_2_4.php)

* [Laravel 12](https://laravel.com/docs/12.x)

* [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum#main-content)

* API

* Tests

* CRUD

* MySQL

* Postman o Bruno

* Visual Studio Code

## Instalación ⚙️

1. Clona este repositorio en tu máquina local usando `git clone https://github.com/nuriadevs/basic-app-laravel-12-sanctum-backend`.

2. Instala las dependencias de PHP usando Composer con `composer install`.

3. Copia el archivo de configuración `.env.example` y configúralo con tu entorno y el archivo `.env`.

4. Genera una clave de aplicación con `php artisan key:generate`.

5. Configura tu base de datos en el archivo `.env`.

6. Ejecuta las migraciones con `php artisan migrate`.

7. Ejecuta los seeds para los datos con `php artisan db:seed`.

8. Inicia el servidor de desarrollo con `php artisan serve`.

9. Accede a la aplicación en tu navegador web en [http://localhost:8000](http://localhost:8000).

10. Endpoints de la API

| Endpoint | **Métodos**| Rutas|

|---------------------|--------|--------------------------|

| **Autenticación** | | |

| Login | POST | api/auth/login |

| Register | POST | api/auth/register |

| Logout | POST | api/auth/logout |

| **Usuarios** | | |

| Index | GET | api/users |

| Show | GET | api/users/{user} |

| Store | POST | api/users |

| Update | PATCH | api/users/{user} |

| Destroy | DELETE | api/users/{user} |

| **Productos** | | |

| Index | GET | api/products |

| Show | GET | api/products/{product} |

| Store | POST | api/products |

| Update | PATCH | api/products/{product} |

| Destroy | DELETE | api/products/{product} |

| **Pedidos** | | |

| Index | GET | api/orders |

| Show | GET | api/orders/{order} |

| Store | POST | api/orders |

| Update | PATCH | api/orders/{order} |

| Destroy | DELETE | api/orders/{order} |

## Variables de Entorno

Visita `.env.example` para crear los entornos.

## Demo Postman :man_astronaut:

<img src="./media/1.png" alt="postman" width="600" />

## Demo Bruno :dog:

<img src="./media/2.png" alt="bruno" width="600" />

## Esquema de la Base de Datos

<img src="./media/bbdd.png" alt="database-schema" width="600" />

## Ver video

<img src="./media/3.png" alt="video" width="600" />

Puedes ver la prueba con Postman de la API Restful en este enlace [Ver video](https://youtu.be/RxrGRVwGSak)

## Enlaces
- [Laravel](https://laravel.com/docs/12.x/releases)
- [Bruno](https://www.usebruno.com/)
- [Postman](https://www.postman.com/)
- [PHP](https://www.php.net/)

## Resumen

- No olvides crear tu propio archivo `.env` para las variables.
- Puedes crear tus propios datos sin ejecutar el seeder.
- Este proyecto está en construcción... puede ser mejorado.

## Gracias 🍀

Muchas gracias por leer este proyecto.
