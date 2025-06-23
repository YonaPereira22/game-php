# Plataforma de Juegos Educativos

Este proyecto es una plataforma escrita en PHP que permite subir, catalogar y jugar juegos educativos basados en HTML5. Incluye un sencillo sistema de administración y votaciones por estrellas.

## Requisitos

- PHP 7.4 o superior con extensiones **PDO** y **ZipArchive** habilitadas.
- Servidor web (por ejemplo Apache o Nginx).
- MySQL o MariaDB.

## Configuración

1. Crea una base de datos vacía llamada `game` y un usuario con los siguientes datos:
   - Usuario: 
   - Contraseña: 

   Datos crearlos a cada ejemplo; simplemente actualiza `config/database.php` con tus credenciales.

2. Clona el repositorio y coloca los archivos en el directorio público de tu servidor web.

3. Asegúrate de que el servidor tenga permisos de escritura en las carpetas `uploads/` y `games/`. Estas carpetas se crean automáticamente cuando subes un juego.

4. Accede a la aplicación desde tu navegador. En la primera carga se crearán las tablas necesarias en la base de datos y se generará un usuario administrador por defecto (usuario `domingo`, contraseña `main1001_Domingo`).

### Juego de ejemplo

El repositorio incluye un juego sencillo llamado **Adivina el Número** en la
carpeta `games/adivina-numero`. Si deseas registrar este juego manualmente en la
base de datos puedes ejecutar el script SQL:

```sql
source db/insert_sample_game.sql;
```

Esto insertará un registro aprobado en la tabla `games` para que aparezca en la
lista principal.

## Uso

- **Inicio** (`index.php`): lista los juegos aprobados. Puedes filtrar por categoría, edad o realizar búsquedas.
- **Subir Juego** (`upload.php`): disponible solo para usuarios con rol *creador* (o administradores). Permite subir un ZIP que contenga el juego (debe incluir un `index.html`).
- **Administración** (`admin.php`): desde aquí se aprueban o eliminan juegos y se gestionan usuarios.
- **Jugar** (`game.php?id=ID`): muestra un iframe con el juego y permite votar con estrellas.
- **Inicio de Sesión** (`login.php`): permite ingresar con perfiles de estudiante, creador o administrador.

## Roles de Usuario

* **estudiante**: puede navegar por los juegos y calificarlos.
* **creador**: además de las funciones del estudiante, puede subir nuevos juegos.
* **admin**: gestiona juegos y usuarios. Puede crear cuentas, aprobar usuarios y cambiar roles entre estudiante y creador.

## Licencia

Este proyecto se distribuye bajo la licencia MIT. Consulta el archivo `LICENSE` para más detalles.

