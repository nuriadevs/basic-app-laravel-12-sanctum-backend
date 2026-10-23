-- SHOP: creación de la base de datos (MySQL 8)
-- ADVERTENCIA: DROP DATABASE elimina la base de datos shop si ya existe.
-- No incluye la tabla `migrations`: no ejecutes `php artisan migrate`.
DROP DATABASE IF EXISTS shop;

CREATE DATABASE shop CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shop;

CREATE TABLE
    users (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        first_surname VARCHAR(255) NOT NULL,
        second_surname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        email_verified_at TIMESTAMP NULL DEFAULT NULL,
        password VARCHAR(255) NOT NULL,
        address VARCHAR(255) NULL,
        phone_number VARCHAR(255) NULL,
        birthdate DATE NULL,
        profile_picture VARCHAR(255) NULL,
        dni VARCHAR(9) NULL,
        postal_code VARCHAR(5) NULL,
        city VARCHAR(255) NULL,
        role ENUM ('admin', 'customer') NOT NULL DEFAULT 'customer',
        is_active TINYINT (1) NOT NULL DEFAULT 1,
        remember_token VARCHAR(100) NULL,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY users_email_unique (email),
        UNIQUE KEY users_dni_unique (dni)
    ) ENGINE = InnoDB;

CREATE TABLE
    products (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        stock INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id)
    ) ENGINE = InnoDB;

CREATE TABLE
    orders (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id),
        KEY orders_user_id_index (user_id),
        CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB;

-- unit_price: precio del producto en el momento de la compra
CREATE TABLE
    order_details (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        order_id BIGINT UNSIGNED NOT NULL,
        product_id BIGINT UNSIGNED NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        total_amount DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id),
        KEY order_details_order_id_index (order_id),
        KEY order_details_product_id_index (product_id),
        CONSTRAINT fk_order_details_order FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT fk_order_details_product FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE = InnoDB;

CREATE TABLE
    password_reset_tokens (
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (email)
    ) ENGINE = InnoDB;

CREATE TABLE
    cache (
        `key` VARCHAR(255) NOT NULL,
        value MEDIUMTEXT NOT NULL,
        expiration INT NOT NULL,
        PRIMARY KEY (`key`)
    ) ENGINE = InnoDB;

CREATE TABLE
    cache_locks (
        `key` VARCHAR(255) NOT NULL,
        owner VARCHAR(255) NOT NULL,
        expiration INT NOT NULL,
        PRIMARY KEY (`key`)
    ) ENGINE = InnoDB;

CREATE TABLE
    jobs (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        queue VARCHAR(255) NOT NULL,
        payload LONGTEXT NOT NULL,
        attempts TINYINT UNSIGNED NOT NULL,
        reserved_at INT UNSIGNED NULL,
        available_at INT UNSIGNED NOT NULL,
        created_at INT UNSIGNED NOT NULL,
        PRIMARY KEY (id),
        KEY jobs_queue_index (queue)
    ) ENGINE = InnoDB;

CREATE TABLE
    failed_jobs (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        uuid VARCHAR(255) NOT NULL,
        connection TEXT NOT NULL,
        queue TEXT NOT NULL,
        payload LONGTEXT NOT NULL,
        exception LONGTEXT NOT NULL,
        failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY failed_jobs_uuid_unique (uuid)
    ) ENGINE = InnoDB;

CREATE TABLE
    personal_access_tokens (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        tokenable_type VARCHAR(255) NOT NULL,
        tokenable_id BIGINT UNSIGNED NOT NULL,
        name TEXT NOT NULL,
        token VARCHAR(64) NOT NULL,
        abilities TEXT NULL,
        last_used_at TIMESTAMP NULL DEFAULT NULL,
        expires_at TIMESTAMP NULL DEFAULT NULL,
        created_at TIMESTAMP NULL DEFAULT NULL,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY personal_access_tokens_token_unique (token),
        KEY personal_access_tokens_tokenable_index (tokenable_type, tokenable_id),
        KEY personal_access_tokens_expires_at_index (expires_at)
    ) ENGINE = InnoDB;

-- DATOS DE PRUEBA (la contraseña de cada usuario está indicada en el comentario previo)
INSERT INTO
    users (
        name,
        first_surname,
        second_surname,
        email,
        password,
        address,
        phone_number,
        birthdate,
        profile_picture,
        dni,
        postal_code,
        city,
        role,
        is_active,
        created_at,
        updated_at
    )
VALUES
    -- laura@example.com | admin | contraseña: Admin1234!
    (
        'Laura',
        'Garcia',
        'Lopez',
        'laura@example.com',
        '$2y$12$tVd.0G27b2lB3E8EqZTVxuKpx9odISTI.On64pTSqcrpbF07lwA0e',
        'Calle Larios 12',
        '600111222',
        '1992-05-14',
        'laura.jpg',
        '12345678Z',
        '29005',
        'Malaga',
        'admin',
        1,
        NOW (),
        NOW ()
    ),
    -- carlos@example.com | cliente | contraseña: Customer1234!
    (
        'Carlos',
        'Martinez',
        'Ruiz',
        'carlos@example.com',
        '$2y$12$.Hd9pExA.M.cm8sD8a.VnOlNUpGaQY2ulvRvlEyZX06ws6QTPWrzG',
        'Avenida Andalucia 45',
        '600222333',
        '1988-09-22',
        'carlos.jpg',
        '23456789D',
        '29006',
        'Malaga',
        'customer',
        1,
        NOW (),
        NOW ()
    ),
    -- ana@example.com | cliente | contraseña: Customer1234!
    (
        'Ana',
        'Fernandez',
        'Sanchez',
        'ana@example.com',
        '$2y$12$.Hd9pExA.M.cm8sD8a.VnOlNUpGaQY2ulvRvlEyZX06ws6QTPWrzG',
        'Calle Granada 8',
        '600333444',
        '1995-12-03',
        NULL,
        '34567890V',
        '18001',
        'Granada',
        'customer',
        1,
        NOW (),
        NOW ()
    ),
    -- david@example.com | cliente | contraseña: Customer1234!
    (
        'David',
        'Lopez',
        'Moreno',
        'david@example.com',
        '$2y$12$.Hd9pExA.M.cm8sD8a.VnOlNUpGaQY2ulvRvlEyZX06ws6QTPWrzG',
        'Calle Real 20',
        '600444555',
        '1985-07-18',
        'david.jpg',
        '45678901G',
        '41001',
        'Sevilla',
        'customer',
        1,
        NOW (),
        NOW ()
    ),
    -- marta@example.com | INACTIVA (is_active = 0) | contraseña: Customer1234!
    (
        'Marta',
        'Ruiz',
        'Navarro',
        'marta@example.com',
        '$2y$12$.Hd9pExA.M.cm8sD8a.VnOlNUpGaQY2ulvRvlEyZX06ws6QTPWrzG',
        'Calle Mayor 31',
        '600555666',
        '2000-02-11',
        NULL,
        '56789012B',
        '28013',
        'Madrid',
        'customer',
        0,
        NOW (),
        NOW ()
    ),
    -- javier@example.com | cliente | contraseña: Customer1234!
    (
        'Javier',
        'Torres',
        'Gil',
        'javier@example.com',
        '$2y$12$.Hd9pExA.M.cm8sD8a.VnOlNUpGaQY2ulvRvlEyZX06ws6QTPWrzG',
        'Plaza Mayor 5',
        '600666777',
        '1990-03-27',
        NULL,
        '67890123B',
        '46001',
        'Valencia',
        'customer',
        1,
        NOW (),
        NOW ()
    ),
    -- lucia@example.com | cliente | contraseña: Customer1234!
    (
        'Lucia',
        'Navarro',
        'Pena',
        'lucia@example.com',
        '$2y$12$.Hd9pExA.M.cm8sD8a.VnOlNUpGaQY2ulvRvlEyZX06ws6QTPWrzG',
        'Calle Alcala 100',
        '600777888',
        '1998-11-09',
        'lucia.jpg',
        '78901234X',
        '28009',
        'Madrid',
        'customer',
        1,
        NOW (),
        NOW ()
    );

INSERT INTO
    products (
        name,
        description,
        price,
        stock,
        created_at,
        updated_at
    )
VALUES
    (
        'Portatil Lenovo IdeaPad',
        'Portatil de 15 pulgadas, 16 GB RAM y SSD de 512 GB',
        649.99,
        15,
        NOW (),
        NOW ()
    ),
    (
        'Raton inalambrico Logitech',
        'Raton inalambrico ergonomico con receptor USB',
        24.90,
        100,
        NOW (),
        NOW ()
    ),
    (
        'Teclado mecanico RGB',
        'Teclado mecanico con iluminacion RGB y switches rojos',
        79.95,
        30,
        NOW (),
        NOW ()
    ),
    (
        'Monitor LG 27 pulgadas',
        'Monitor Full HD IPS de 27 pulgadas',
        189.50,
        20,
        NOW (),
        NOW ()
    ),
    (
        'Auriculares Bluetooth',
        'Auriculares inalambricos con cancelacion de ruido y microfono',
        59.99,
        45,
        NOW (),
        NOW ()
    ),
    (
        'Webcam HD',
        'Camara web 1080p para videollamadas',
        39.95,
        25,
        NOW (),
        NOW ()
    ),
    (
        'Hub USB-C 7 en 1',
        'Adaptador USB-C con HDMI, USB 3.0 y lector de tarjetas',
        34.90,
        60,
        NOW (),
        NOW ()
    ),
    (
        'Disco SSD externo 1 TB',
        'SSD portatil USB 3.2 de 1 TB',
        99.00,
        35,
        NOW (),
        NOW ()
    ),
    (
        'Silla ergonomica de oficina',
        'Silla con soporte lumbar y reposabrazos regulables',
        229.00,
        8,
        NOW (),
        NOW ()
    ),
    (
        'Alfombrilla XL',
        'Alfombrilla de escritorio de 90x40 cm',
        14.95,
        80,
        NOW (),
        NOW ()
    ),
    (
        'Altavoz Bluetooth portatil',
        'Altavoz resistente al agua con 12 horas de bateria',
        49.90,
        0,
        NOW (),
        NOW ()
    ),
    (
        'Soporte para portatil',
        'Soporte de aluminio plegable y ajustable',
        29.90,
        50,
        NOW (),
        NOW ()
    );

INSERT INTO
    orders (user_id, total_amount, created_at, updated_at)
VALUES
    (
        2,
        674.89,
        '2026-03-01 10:15:00',
        '2026-03-01 10:15:00'
    ),
    (
        2,
        159.93,
        '2026-03-05 16:30:00',
        '2026-03-05 16:30:00'
    ),
    (
        3,
        269.45,
        '2026-03-10 12:00:00',
        '2026-03-10 12:00:00'
    ),
    (
        4,
        649.99,
        '2026-03-15 18:45:00',
        '2026-03-15 18:45:00'
    ),
    (
        1,
        149.74,
        '2026-03-20 09:20:00',
        '2026-03-20 09:20:00'
    ),
    (
        6,
        168.80,
        '2026-04-02 11:05:00',
        '2026-04-02 11:05:00'
    ),
    (
        7,
        273.85,
        '2026-04-08 19:40:00',
        '2026-04-08 19:40:00'
    ),
    (
        3,
        104.85,
        '2026-04-12 08:55:00',
        '2026-04-12 08:55:00'
    );

INSERT INTO
    order_details (
        order_id,
        product_id,
        quantity,
        unit_price,
        total_amount,
        created_at,
        updated_at
    )
VALUES
    (
        1,
        1,
        1,
        649.99,
        649.99,
        '2026-03-01 10:15:00',
        '2026-03-01 10:15:00'
    ),
    (
        1,
        2,
        1,
        24.90,
        24.90,
        '2026-03-01 10:15:00',
        '2026-03-01 10:15:00'
    ),
    (
        2,
        5,
        2,
        59.99,
        119.98,
        '2026-03-05 16:30:00',
        '2026-03-05 16:30:00'
    ),
    (
        2,
        6,
        1,
        39.95,
        39.95,
        '2026-03-05 16:30:00',
        '2026-03-05 16:30:00'
    ),
    (
        3,
        4,
        1,
        189.50,
        189.50,
        '2026-03-10 12:00:00',
        '2026-03-10 12:00:00'
    ),
    (
        3,
        3,
        1,
        79.95,
        79.95,
        '2026-03-10 12:00:00',
        '2026-03-10 12:00:00'
    ),
    (
        4,
        1,
        1,
        649.99,
        649.99,
        '2026-03-15 18:45:00',
        '2026-03-15 18:45:00'
    ),
    (
        5,
        2,
        2,
        24.90,
        49.80,
        '2026-03-20 09:20:00',
        '2026-03-20 09:20:00'
    ),
    (
        5,
        6,
        1,
        39.95,
        39.95,
        '2026-03-20 09:20:00',
        '2026-03-20 09:20:00'
    ),
    (
        5,
        5,
        1,
        59.99,
        59.99,
        '2026-03-20 09:20:00',
        '2026-03-20 09:20:00'
    ),
    (
        6,
        8,
        1,
        99.00,
        99.00,
        '2026-04-02 11:05:00',
        '2026-04-02 11:05:00'
    ),
    (
        6,
        7,
        2,
        34.90,
        69.80,
        '2026-04-02 11:05:00',
        '2026-04-02 11:05:00'
    ),
    (
        7,
        9,
        1,
        229.00,
        229.00,
        '2026-04-08 19:40:00',
        '2026-04-08 19:40:00'
    ),
    (
        7,
        12,
        1,
        29.90,
        29.90,
        '2026-04-08 19:40:00',
        '2026-04-08 19:40:00'
    ),
    (
        7,
        10,
        1,
        14.95,
        14.95,
        '2026-04-08 19:40:00',
        '2026-04-08 19:40:00'
    ),
    (
        8,
        3,
        1,
        79.95,
        79.95,
        '2026-04-12 08:55:00',
        '2026-04-12 08:55:00'
    ),
    (
        8,
        2,
        1,
        24.90,
        24.90,
        '2026-04-12 08:55:00',
        '2026-04-12 08:55:00'
    );