<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$usuarioActual = htmlspecialchars($_SESSION['usuario']);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Automotor Express | Mi cuenta</title>
        <meta name="description" content="Área personal de Automotor Express." />
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
                    <div class="nav-usuario">
                        <span class="nav-usuario-texto">Hola, <?php echo $usuarioActual; ?></span>
                        <a href="bienvenida.php">Mi cuenta</a>
                        <a href="cerrarsesion.php">Cerrar sesión</a>
                    </div>
                </nav>
            </div>
        </header>

        <main class="pagina-secundaria">
            <section class="encabezado-pagina">
                <div class="contenedor">
                    <p class="etiqueta-seccion">Mi cuenta</p>
                    <h1 class="titulo-principal titulo-pagina">¡Bienvenido, <?php echo $usuarioActual; ?>!</h1>
                    <p class="texto-principal texto-pagina">
                        Has iniciado sesión correctamente en Automotor Express. Aquí puedes seguir navegando por nuestros servicios y volver al inicio cuando quieras.
                    </p>
                </div>
            </section>

            <section class="seccion-acceso">
                <div class="contenedor">
                    <div class="panel-acceso">
                        <div class="encabezado-seccion">
                            <p class="etiqueta-seccion">Cuenta</p>
                            <h2>Panel de usuario</h2>
                        </div>

                        <p style="margin: 0 0 1rem; color: rgba(229, 231, 235, 0.82);">
                            Tu sesión está activa y puedes continuar navegando por la página.
                        </p>

                        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
                            <a href="index.html" class="boton-principal">Volver al inicio</a>
                            <a href="cerrarsesion.php" class="boton-principal">Cerrar sesión</a>
                        </div>
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
