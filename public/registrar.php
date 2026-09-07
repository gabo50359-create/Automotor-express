<?php
session_start();
include "../php/conexion.php";

$usuarioIngresado = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioIngresado = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar_password = $_POST['confirmar_password'] ?? '';

    if ($usuarioIngresado === '' || $password === '' || $confirmar_password === '') {
        $error = 'Completa todos los campos.';
    } elseif (strlen($usuarioIngresado) < 3) {
        $error = 'El usuario debe tener al menos 3 caracteres.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirmar_password) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $consulta = $conexion->prepare("SELECT usuario FROM usuarios WHERE usuario = ? LIMIT 1");
        $consulta->bind_param("s", $usuarioIngresado);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {
            $error = 'El usuario ya existe.';
        } else {
            $password_cifrado = password_hash($password, PASSWORD_DEFAULT);
            $insercion = $conexion->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
            $insercion->bind_param("ss", $usuarioIngresado, $password_cifrado);

            if ($insercion->execute()) {
                $_SESSION['usuario'] = $usuarioIngresado;
                setcookie('usuario', $usuarioIngresado, time() + 86400, '/');
                header("location: bienvenida.php");
                exit();
            }

            $error = 'Error al registrar el usuario. Inténtalo de nuevo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Automotor Express | Registro</title>
        <meta name="description" content="Crea tu cuenta en Automotor Express para acceder a servicios del taller." />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="css/styles-principal.css" />
        <script src="js/site.js" defer></script>
    </head>
    <body>
        <div class="fondo-parallax" aria-hidden="true"></div>

        <header class="encabezado">
            <div class="contenedor navegacion">
                <a href="index.html" class="logotipo" aria-label="Automotor Express inicio">
                    <img class="imagen-logo" src="img/logo2.png" alt="Automotor Express" />
                </a>

                <nav class="menu-navegacion" aria-label="Menú principal">
                    <a href="index.html">Inicio</a>
                    <a href="servicios.html">Servicios</a>
                    <a href="catalogo.html">Catálogo</a>
                    <a href="nosotros.html">Nosotros</a>
                    <a href="contacto.html">Contacto</a>
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <a href="bienvenida.php">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></a>
                        <a href="cerrarsesion.php">Cerrar sesión</a>
                    <?php else: ?>
                        <a href="login.html">Login</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>

        <main class="pagina-secundaria">
            <section class="encabezado-pagina">
                <div class="contenedor">
                    <p class="etiqueta-seccion">Acceso</p>
                    <h1 class="titulo-principal titulo-pagina">Crea tu cuenta</h1>
                    <p class="texto-principal texto-pagina">
                        Regístrate para agendar servicios, consultar información y cuidar mejor tu vehículo.
                    </p>
                </div>
            </section>

            <section class="seccion-acceso">
                <div class="contenedor contenedor-acceso">
                    <div class="panel-acceso">
                        <div class="encabezado-seccion">
                            <p class="etiqueta-seccion">Registro</p>
                            <h2>Crear cuenta</h2>
                        </div>

                        <?php if ($error !== ''): ?>
                            <div class="mensaje-error" style="background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; text-align: center; font-weight: 600;">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form class="formulario-acceso" method="post" action="registrar.php">
                            <div class="campo-formulario">
                                <label for="usuario-registro">Usuario</label>
                                <input type="text" id="usuario-registro" name="usuario" placeholder="Nuevo usuario" value="<?php echo htmlspecialchars($usuarioIngresado); ?>" required />
                            </div>

                            <div class="campo-formulario">
                                <label for="contrasena-registro">Contraseña</label>
                                <input type="password" id="contrasena-registro" name="password" placeholder="********" required />
                            </div>

                            <div class="campo-formulario">
                                <label for="confirmar-contrasena">Confirmar contraseña</label>
                                <input type="password" id="confirmar-contrasena" name="confirmar_password" placeholder="********" required />
                            </div>

                            <button type="submit" class="boton-principal boton-formulario">Registrarse</button>
                            <p class="enlace-formulario"><a href="index.php">¿Ya tienes cuenta? Inicia sesión</a></p>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <footer class="pie-pagina" id="contacto">
            <div class="contenedor pie-contenido">
                <div>
                    <p class="nombre-empresa">Automotor Express © 2026</p>
                    <p>Teléfono: +57 1 2345678</p>
                    <p>Email: info@automotorexpress.com</p>
                    <p>Dirección: Cra 50 #12-30, Bogotá</p>
                </div>

                <div class="redes-sociales">
                    <a href="#" aria-label="Facebook">Facebook</a>
                    <a href="#" aria-label="Instagram">Instagram</a>
                    <a href="#" aria-label="WhatsApp">WhatsApp</a>
                    <a href="#" aria-label="Twitter">Twitter</a>
                </div>
            </div>
        </footer>
    </body>
</html>
