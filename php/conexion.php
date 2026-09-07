<?php
$servidorBD = getenv('MYSQLHOST') ?: 'localhost';
$puertoBD = (int) (getenv('MYSQLPORT') ?: 3306);
$usuarioBD = getenv('MYSQLUSER') ?: 'root';
$contraseñaBD = getenv('MYSQLPASSWORD') ?: '';
$nombreBD = getenv('MYSQLDATABASE') ?: 'automotor_express';

if (getenv('MYSQLHOST')) {
    $conexion = new mysqli($servidorBD, $usuarioBD, $contraseñaBD, $nombreBD, $puertoBD);
} else {
    $conexion = new mysqli($servidorBD, $usuarioBD, $contraseñaBD, '', $puertoBD);
}

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (!getenv('MYSQLHOST')) {
    $nombreBDSeguro = $conexion->real_escape_string($nombreBD);
    if (!$conexion->query("CREATE DATABASE IF NOT EXISTS `$nombreBDSeguro` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        die("Error al crear la base de datos: " . $conexion->error);
    }

    $conexion->select_db($nombreBD);
}

$conexion->set_charset("utf8");

$conexion->query("CREATE TABLE IF NOT EXISTS categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conexion->query("INSERT IGNORE INTO categorias (id, nombre) VALUES
    (1, 'Motor'),
    (2, 'Frenos'),
    (3, 'Suspension'),
    (4, 'Electrico')");

$conexion->query("CREATE TABLE IF NOT EXISTS repuestos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0,
    imagen VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria_id INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    INDEX idx_repuestos_categoria (categoria_id),
    INDEX idx_repuestos_tipo (tipo),
    CONSTRAINT fk_repuestos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

function passwordEsValida($passwordIngresada, $passwordGuardada) {
    if ($passwordGuardada === null || $passwordGuardada === '') {
        return false;
    }

    if (password_get_info($passwordGuardada)['algo'] !== 0) {
        return password_verify($passwordIngresada, $passwordGuardada);
    }

    return hash_equals($passwordGuardada, $passwordIngresada);
}

$conexion->query("CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conexion->query("CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    telefono VARCHAR(40) DEFAULT '',
    asunto VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contactos_email (email),
    INDEX idx_contactos_fecha (creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conexion->query("CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    marca VARCHAR(100) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    vehiculo ENUM('Carro', 'Moto', 'Accesorio') NOT NULL,
    referencia VARCHAR(100) NOT NULL UNIQUE,
    codigo VARCHAR(100) NOT NULL UNIQUE,
    compatibilidad VARCHAR(180) NOT NULL,
    precio DECIMAL(12, 2) NOT NULL DEFAULT 0,
    imagen VARCHAR(500) NOT NULL,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    vendidos INT NOT NULL DEFAULT 0,
    reciente DATE NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_productos_nombre (nombre),
    INDEX idx_productos_marca (marca),
    INDEX idx_productos_categoria (categoria),
    INDEX idx_productos_vehiculo (vehiculo),
    INDEX idx_productos_precio (precio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conexion->query("INSERT IGNORE INTO productos
    (nombre, descripcion, marca, categoria, vehiculo, referencia, codigo, compatibilidad, precio, imagen, destacado, stock, vendidos, reciente)
    VALUES
    ('Aceite de Moto 10W-40', 'Aceite sintético para protección y limpieza interna del motor.', 'Motrix Demo', 'Motor', 'Moto', 'MOT-MOT-001', 'AE-M1-001', 'Motos demo 100-250 cc', 38000, 'https://images.unsplash.com/photo-1558980664-10e7170b5df9?auto=format&fit=crop&w=900&q=80', 1, 12, 85, '2026-01-10'),
    ('Filtro de Aceite', 'Filtración eficiente para conservar el aceite limpio y proteger el motor.', 'Autotek Demo', 'Motor', 'Carro', 'CAR-MOT-001', 'AE-C1-001', 'Modelos demo 2016-2022', 42000, 'https://images.unsplash.com/photo-1632903055463-1e4adf4c8b2d?auto=format&fit=crop&w=900&q=80', 0, 18, 63, '2026-02-08'),
    ('Pastillas de Freno', 'Frenado estable y progresivo para una conducción segura.', 'RoadPro Demo', 'Frenos', 'Carro', 'CAR-FRE-001', 'AE-C4-001', 'Modelos demo 2018-2024', 125000, 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=900&q=80', 1, 9, 112, '2026-02-18'),
    ('Llanta Urbana', 'Llanta resistente para recorridos urbanos y uso diario.', 'Express Parts', 'Llantas', 'Moto', 'MOT-LLA-001', 'AE-M3-001', 'Motos demo 125-300 cc', 185000, 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=900&q=80', 0, 14, 47, '2026-03-04'),
    ('Batería 12V', 'Batería confiable para arranque y funcionamiento del sistema eléctrico.', 'NovaDrive Demo', 'Partes electricas', 'Carro', 'CAR-PAR-001', 'AE-C5-001', 'Modelos demo 2015-2023', 310000, 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?auto=format&fit=crop&w=900&q=80', 1, 7, 91, '2026-03-12'),
    ('Amortiguador Trasero', 'Componente para recuperar estabilidad y confort en la suspensión.', 'FerroMax Demo', 'Suspension', 'Moto', 'MOT-SUS-001', 'AE-M2-001', 'Motos demo 150-500 cc', 210000, 'https://images.unsplash.com/photo-1553440569-bcc63803a83d?auto=format&fit=crop&w=900&q=80', 0, 11, 38, '2026-03-20')");

$conexion->query("INSERT IGNORE INTO productos
    (nombre, descripcion, marca, categoria, vehiculo, referencia, codigo, compatibilidad, precio, imagen, destacado, stock, vendidos, reciente)
    VALUES ('Correa de Distribución', 'Repuesto confiable para el sistema de distribución.', 'Autotek Demo', 'Motor', 'Carro', 'CAR-COR-001', 'AE-C1-002', 'Modelos demo 2016-2022', 80000, 'img/catalogo.repuestos/Correa.jpg', 0, 10, 34, '2026-03-25')");

$conexion->query("UPDATE productos SET imagen = CASE referencia
    WHEN 'MOT-MOT-001' THEN 'img/repuestos-originales.jpg'
    WHEN 'CAR-MOT-001' THEN 'img/catalogo.repuestos/filtro.jpg'
    WHEN 'CAR-FRE-001' THEN 'img/catalogo.repuestos/pastillas.freno.jpg'
    WHEN 'MOT-LLA-001' THEN 'img/catalogo.repuestos/llantas.jpg'
    WHEN 'CAR-PAR-001' THEN 'img/catalogo.repuestos/bateria.jpg'
    WHEN 'MOT-SUS-001' THEN 'img/mecanica-general.jpg'
    ELSE imagen
END WHERE referencia IN ('MOT-MOT-001', 'CAR-MOT-001', 'CAR-FRE-001', 'MOT-LLA-001', 'CAR-PAR-001', 'MOT-SUS-001')");

$conexion->query("INSERT INTO repuestos (nombre, precio, imagen, descripcion, stock, categoria_id, tipo)
    SELECT p.nombre, p.precio, p.imagen, p.descripcion, p.stock,
        CASE p.categoria
            WHEN 'Frenos' THEN 2
            WHEN 'Suspension' THEN 3
            WHEN 'Partes electricas' THEN 4
            ELSE 1
        END,
        p.vehiculo
    FROM productos p
    WHERE NOT EXISTS (
        SELECT 1 FROM repuestos r WHERE r.nombre = p.nombre AND r.tipo = p.vehiculo
    )");

$conexion->query("CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    nombre_cliente VARCHAR(120) NOT NULL,
    email_cliente VARCHAR(160) NOT NULL,
    telefono_cliente VARCHAR(40) NOT NULL,
    total DECIMAL(12, 2) NOT NULL,
    estado ENUM('Pendiente', 'Confirmado', 'Cancelado') NOT NULL DEFAULT 'Pendiente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pedidos_producto (producto_id),
    INDEX idx_pedidos_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
?>
