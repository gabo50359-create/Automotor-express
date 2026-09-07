# Despliegue en Railway

## 1. Crear los servicios

1. En Railway crea un proyecto nuevo.
2. Añade un servicio **MySQL** desde `Add` > `Database` > `MySQL`.
3. Añade el repositorio de GitHub `gabo50359-create/Automotor-express` como servicio de aplicación.

Railway detectará el `Dockerfile` y construirá la aplicación PHP con Apache.

## 2. Conectar la base de datos

En el servicio de la aplicación, abre `Variables` y usa `Add Reference` para agregar estas variables desde el servicio MySQL:

```text
MYSQLHOST=${{MySQL.MYSQLHOST}}
MYSQLPORT=${{MySQL.MYSQLPORT}}
MYSQLUSER=${{MySQL.MYSQLUSER}}
MYSQLPASSWORD=${{MySQL.MYSQLPASSWORD}}
MYSQLDATABASE=${{MySQL.MYSQLDATABASE}}
```

Si Railway muestra otro nombre para el servicio MySQL, reemplaza `MySQL` en las referencias por ese nombre.

La aplicación crea sus tablas (`usuarios`, `contactos`, productos y demás) al iniciar. No uses la contraseña local de XAMPP en Railway.

## 3. Dominio

En `Settings` > `Networking` > `Generate Domain`, genera el dominio público de Railway. La aplicación escuchará el puerto que Railway proporcione mediante `PORT`.

## Desarrollo local

En XAMPP se mantienen los valores locales (`root`, contraseña vacía y base `automotor_express`). El archivo `.env.example` documenta las variables disponibles, pero no contiene credenciales reales.