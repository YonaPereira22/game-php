# Plataforma de Juegos Educativos

Este proyecto es una plataforma escrita en PHP que permite subir, catalogar y jugar juegos educativos basados en HTML5. Incluye un sencillo sistema de administración y votaciones por estrellas.

## Requisitos

- PHP 7.4 o superior con extensiones **PDO** y **ZipArchive** habilitadas.
- Servidor web (por ejemplo Apache o Nginx).
- MySQL o MariaDB.

## Configuración

1. Crea una base de datos vacía llamada `u952965051_game` y un usuario con los siguientes datos:
   - Usuario: `u952965051_game`
   - Contraseña: `main1001_Game`

   Puedes utilizar otros datos si lo prefieres; simplemente actualiza `config/database.php` con tus credenciales.

2. Clona el repositorio y coloca los archivos en el directorio público de tu servidor web.

3. Asegúrate de que el servidor tenga permisos de escritura en las carpetas `uploads/` y `games/`. Estas carpetas se crean automáticamente cuando subes un juego.

4. Accede a la aplicación desde tu navegador. En la primera carga se crearán las tablas necesarias en la base de datos.

## Uso

- **Inicio** (`index.php`): lista los juegos aprobados. Puedes filtrar por categoría, edad o realizar búsquedas.
- **Subir Juego** (`upload.php`): permite a los usuarios subir un archivo ZIP que contenga el juego (debe incluir un `index.html`).
- **Administración** (`admin.php`): desde aquí puedes aprobar o eliminar los juegos cargados.
- **Jugar** (`game.php?id=ID`): muestra un iframe con el juego y permite votar con estrellas.

## Licencia

Este proyecto se distribuye bajo la licencia MIT. Consulta el archivo `LICENSE` para más detalles.

