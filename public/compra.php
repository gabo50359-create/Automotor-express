<?php
require_once __DIR__ . '/../php/conexion.php';

$productoId = filter_input(INPUT_GET, 'producto', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
$producto = null;
$mensaje = '';
$error = '';

if ($productoId) {
    $consulta = $conexion->prepare('SELECT id, nombre, precio, stock, imagen FROM repuestos WHERE id = ? LIMIT 1');
    $consulta->bind_param('i', $productoId);
}
if (isset($consulta)) {
    $consulta->execute();
    $producto = $consulta->get_result()->fetch_assoc();
}

if (!$producto) {
    http_response_code(404);
    $error = 'El producto no existe.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    $cantidad = $cantidad ?: 1;

    if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $telefono === '') {
        $error = 'Completa tu nombre, un email válido y tu teléfono.';
    } elseif ($cantidad < 1 || $cantidad > (int) $producto['stock']) {
        $error = 'La cantidad solicitada no está disponible.';
    } else {
        $total = (float) $producto['precio'] * $cantidad;
        $conexion->begin_transaction();
        $pedido = $conexion->prepare('INSERT INTO pedidos (producto_id, cantidad, nombre_cliente, email_cliente, telefono_cliente, total) VALUES (?, ?, ?, ?, ?, ?)');
        $pedido->bind_param('iisssd', $productoId, $cantidad, $nombre, $email, $telefono, $total);
        $actualizar = $conexion->prepare('UPDATE repuestos SET stock = stock - ? WHERE id = ? AND stock >= ?');
        $actualizar->bind_param('iii', $cantidad, $productoId, $cantidad);

        if ($pedido->execute() && $actualizar->execute() && $actualizar->affected_rows === 1) {
            $conexion->commit();
            $mensaje = 'Compra registrada correctamente. Nos comunicaremos contigo para confirmar el pedido.';
            $producto['stock'] -= $cantidad;
        } else {
            $conexion->rollback();
            $error = 'No fue posible registrar la compra. Inténtalo de nuevo.';
        }
    }
}

$moneda = static fn (float $precio): string => '$' . number_format($precio, 0, ',', '.');
$escapar = static fn (string $texto): string => htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Automotor Express | Comprar</title>
    <link rel="stylesheet" href="css/style-general.css" />
    <link rel="stylesheet" href="css/style-header.css" />
    <link rel="stylesheet" href="css/style-footer.css" />
    <style>
        .compra-pagina { padding: 8rem 0 4rem; }
        .compra-caja { display: grid; grid-template-columns: minmax(220px, .8fr) 1.2fr; gap: 2rem; max-width: 900px; margin-top: 1.5rem; padding: 1.5rem; background: rgba(17,17,17,.88); border: 1px solid var(--color-borde); border-radius: 8px; }
        .compra-caja img { width: 100%; height: 240px; object-fit: cover; border-radius: 6px; }
        .compra-caja h1 { margin-top: 1rem; font-size: 1.5rem; }
        .compra-precio { color: var(--color-acento); font-size: 1.3rem; font-weight: 700; }
        .formulario-compra { display: grid; gap: 1rem; }
        .formulario-compra label { display: grid; gap: .35rem; color: var(--color-texto); }
        .formulario-compra input { min-height: 42px; padding: .65rem .75rem; border: 1px solid var(--color-borde); border-radius: 6px; background: #111827; color: var(--color-secundario); }
        .formulario-compra button { padding: .75rem 1rem; border: 0; border-radius: 6px; background: var(--color-acento); color: #fff; cursor: pointer; font-weight: 700; }
        .mensaje-compra { margin-top: 1rem; padding: 1rem; border-radius: 6px; background: rgba(34,197,94,.16); color: #86efac; }
        .error-compra { margin-top: 1rem; padding: 1rem; border-radius: 6px; background: rgba(220,38,38,.16); color: #fca5a5; }
        @media (max-width: 700px) { .compra-caja { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header class="encabezado"><div class="contenedor navegacion"><a href="index.html" class="logotipo"><img class="imagen-logo" src="img/logo2.png" alt="Automotor Express" /></a><nav class="menu-navegacion"><a href="index.html">Inicio</a><a href="catalogo.html">Catálogo</a><a href="login.html">Login</a></nav></div></header>
<main class="compra-pagina"><section class="contenedor"><p class="etiqueta-seccion">Compra</p><h1 class="titulo-principal">Completa tu solicitud</h1>
<?php if ($producto): ?>
    <div class="compra-caja"><div><img src="<?= $escapar($producto['imagen']) ?>" alt="<?= $escapar($producto['nombre']) ?>" /><h2><?= $escapar($producto['nombre']) ?></h2><p class="compra-precio"><?= $moneda((float) $producto['precio']) ?></p></div><div>
        <?php if ($mensaje): ?><div class="mensaje-compra"><?= $escapar($mensaje) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="error-compra"><?= $escapar($error) ?></div><?php endif; ?>
        <?php if (!$mensaje): ?><form class="formulario-compra" method="post"><input type="hidden" name="producto_id" value="<?= (int) $producto['id'] ?>" /><label>Nombre<input name="nombre" required value="<?= $escapar($_POST['nombre'] ?? '') ?>" /></label><label>Email<input type="email" name="email" required value="<?= $escapar($_POST['email'] ?? '') ?>" /></label><label>Teléfono<input type="tel" name="telefono" required value="<?= $escapar($_POST['telefono'] ?? '') ?>" /></label><label>Cantidad<input type="number" name="cantidad" min="1" max="<?= (int) $producto['stock'] ?>" value="1" required /></label><button type="submit">Confirmar compra</button></form><?php endif; ?>
    </div></div>
<?php else: ?><p class="error-compra"><?= $escapar($error) ?></p><?php endif; ?>
</section></main>
</body>
</html>
