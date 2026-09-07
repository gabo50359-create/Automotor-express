<?php
require_once __DIR__ . '/../php/conexion.php';

$buscar = trim($_GET['buscar'] ?? '');
$vehiculo = $_GET['tipo'] ?? '';
$categoria = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: 0;
$precioMinimo = filter_input(INPUT_GET, 'precio_minimo', FILTER_VALIDATE_FLOAT);
$precioMaximo = filter_input(INPUT_GET, 'precio_maximo', FILTER_VALIDATE_FLOAT);
$destacados = isset($_GET['destacados']);

$categorias = [];
$catalogo = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre");
if ($catalogo) {
    while ($fila = $catalogo->fetch_assoc()) {
        $categorias[] = $fila;
    }
}

$sql = "SELECT r.*, c.nombre AS categoria_nombre
    FROM repuestos r
    INNER JOIN categorias c ON c.id = r.categoria_id
    WHERE 1=1";
$tipos = '';
$valores = [];

if ($buscar !== '') {
    $sql .= " AND (r.nombre LIKE CONCAT('%', ?, '%') OR r.descripcion LIKE CONCAT('%', ?, '%'))";
    $tipos .= 'ss';
    $valores[] = $buscar;
    $valores[] = $buscar;
}
if (in_array($vehiculo, ['Carro', 'Moto'], true)) {
    $sql .= " AND r.tipo = ?";
    $tipos .= 's';
    $valores[] = $vehiculo;
}
if ($categoria > 0) {
    $sql .= " AND r.categoria_id = ?";
    $tipos .= 'i';
    $valores[] = $categoria;
}
if ($precioMinimo !== false && $precioMinimo !== null) {
    $sql .= " AND precio >= ?";
    $tipos .= 'd';
    $valores[] = $precioMinimo;
}
if ($precioMaximo !== false && $precioMaximo !== null) {
    $sql .= " AND precio <= ?";
    $tipos .= 'd';
    $valores[] = $precioMaximo;
}
if ($destacados) {
    $sql .= " AND destacado = 1";
}
$sql .= " ORDER BY destacado DESC, nombre ASC";

$sentencia = $conexion->prepare($sql);
if ($tipos !== '') {
    $sentencia->bind_param($tipos, ...$valores);
}
$sentencia->execute();
$productos = $sentencia->get_result();
$moneda = static fn (float $precio): string => '$' . number_format($precio, 0, ',', '.');
$escapar = static fn (string $texto): string => htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Automotor Express | Tienda demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style-general.css" />
    <link rel="stylesheet" href="css/style-header.css" />
    <link rel="stylesheet" href="css/style-footer.css" />
    <link rel="stylesheet" href="css/style-catalogo-avanzado.css" />
    <style>
        .tienda-php { padding: 8rem 0 4rem; }
        .filtros-php { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)) auto; gap: .8rem; padding: 1rem; margin: 1.5rem 0; background: rgba(17,17,17,.88); border: 1px solid var(--linea); border-radius: 8px; }
        .filtros-php input, .filtros-php select { min-height: 42px; width: 100%; padding: .65rem .75rem; border: 1px solid rgba(229,231,235,.16); border-radius: 6px; background: #111827; color: var(--secundario); }
        .filtros-php button { min-height: 42px; padding: .65rem .9rem; border: 1px solid var(--acento); border-radius: 6px; background: transparent; color: var(--acento); cursor: pointer; }
        .resultados-php { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.2rem; }
        .producto-php { overflow: hidden; background: rgba(17,17,17,.88); border: 1px solid var(--linea); border-radius: 8px; }
        .producto-php img { width: 100%; height: 190px; object-fit: cover; }
        .producto-php-contenido { display: flex; min-height: 210px; padding: 1rem; flex-direction: column; }
        .producto-php-contenido small { color: var(--color-acento); font-weight: 700; }
        .producto-php h2 { margin: .4rem 0; font-size: 1.1rem; color: var(--secundario); }
        .producto-php p { color: var(--texto-suave); }
        .precio-producto-php { display: block; position: static; clear: both; margin: auto 0 0; padding-top: 1rem; color: var(--color-acento); font-family: 'Space Mono', monospace; font-size: 1.25rem; font-weight: 700; line-height: 1.2; white-space: nowrap; }
        .boton-comprar-php { width: 100%; margin-top: 1rem; padding: .7rem 1rem; border: 0; border-radius: 6px; background: var(--color-acento); color: #fff; cursor: pointer; font-weight: 700; }
        .boton-comprar-php:hover { background: var(--color-primario); }
        .agotado-php { display: block; margin-top: 1rem; color: #fca5a5; font-weight: 700; }
        .sin-resultados-php { padding: 2rem; color: var(--texto-suave); }
        @media (max-width: 900px) { .filtros-php { grid-template-columns: repeat(2, 1fr); } .resultados-php { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .filtros-php, .resultados-php { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header class="encabezado">
    <div class="contenedor navegacion">
        <a href="index.html" class="logotipo" aria-label="Automotor Express inicio"><img class="imagen-logo" src="img/logo2.png" alt="Automotor Express" /></a>
        <nav class="menu-navegacion" aria-label="Menú principal">
            <a href="index.html">Inicio</a><a href="servicios.html">Servicios</a><a href="catalogo.html">Catálogo</a><a href="nosotros.html">Nosotros</a><a href="contacto.html">Contacto</a><a href="login.html">Login</a>
        </nav>
    </div>
</header>
<main class="tienda-php">
    <section class="contenedor">
        <p class="etiqueta-seccion">Tienda demo</p>
        <h1 class="titulo-principal">Repuestos, partes y accesorios</h1>
        <p class="descripcion-catalogo">Busca repuestos por nombre, categoría o tipo de vehículo.</p>
        <form class="filtros-php" method="get" action="catalogo.php">
            <input type="search" name="buscar" value="<?= $escapar($buscar) ?>" placeholder="Buscar repuesto" aria-label="Buscar repuesto" />
            <select name="tipo" aria-label="Filtrar por vehículo"><option value="">Todos los vehículos</option><?php foreach (['Carro', 'Moto'] as $opcion): ?><option value="<?= $opcion ?>" <?= $vehiculo === $opcion ? 'selected' : '' ?>><?= $opcion ?></option><?php endforeach; ?></select>
            <select name="categoria" aria-label="Filtrar por categoría"><option value="0">Todas las categorías</option><?php foreach ($categorias as $opcion): ?><option value="<?= (int) $opcion['id'] ?>" <?= $categoria === (int) $opcion['id'] ? 'selected' : '' ?>><?= $escapar($opcion['nombre']) ?></option><?php endforeach; ?></select>
            <input type="number" name="precio_minimo" min="0" value="<?= $precioMinimo !== false && $precioMinimo !== null ? $precioMinimo : '' ?>" placeholder="Precio mínimo" aria-label="Precio mínimo" />
            <input type="number" name="precio_maximo" min="0" value="<?= $precioMaximo !== false && $precioMaximo !== null ? $precioMaximo : '' ?>" placeholder="Precio máximo" aria-label="Precio máximo" />
            <button type="submit">Buscar</button>
        </form>
        <p class="descripcion-catalogo"><?= $productos->num_rows ?> productos encontrados</p>
        <div class="resultados-php">
            <?php if ($productos->num_rows === 0): ?><p class="sin-resultados-php">No encontramos productos con esos filtros.</p><?php endif; ?>
            <?php while ($producto = $productos->fetch_assoc()): ?>
                <article class="producto-php">
                    <img src="<?= $escapar($producto['imagen']) ?>" alt="<?= $escapar($producto['nombre']) ?>" loading="lazy" />
                    <div class="producto-php-contenido">
                        <small><?= $escapar($producto['tipo']) ?> · Categoría: <?= $escapar($producto['categoria_nombre']) ?></small>
                        <h2><?= $escapar($producto['nombre']) ?></h2>
                        <p><?= $escapar($producto['descripcion']) ?></p>
                        <p class="precio-producto-php"><?= $moneda((float) $producto['precio']) ?></p>
                        <?php if ((int) $producto['stock'] > 0): ?>
                            <form action="compra.php" method="get">
                                <input type="hidden" name="producto" value="<?= (int) $producto['id'] ?>" />
                                <button class="boton-comprar-php" type="submit">Comprar</button>
                            </form>
                        <?php else: ?>
                            <span class="agotado-php">Agotado</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
</main>
</body>
</html>
