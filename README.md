# Plataforma de Juegos Educativos

Este repositorio alberga una plataforma escrita en PHP para subir, catalogar y jugar producciones educativas basadas en HTML5. El sistema incluye un panel de administración sencillo y permite a los usuarios valorar cada juego.

## Parte técnica

### Requisitos

- PHP 7.4 o superior con las extensiones **PDO** y **ZipArchive** habilitadas.
- Servidor web (Apache, Nginx u otro compatible).
- MySQL o MariaDB como base de datos.

### Configuración

1. Crea una base de datos llamada `game` y actualiza `config/database.php` con tus credenciales.
2. Clona este repositorio y coloca los archivos en el directorio público de tu servidor.
3. Asegúrate de que el servidor tenga permisos de escritura sobre las carpetas `uploads/` y `games/`.
4. Al ingresar por primera vez se crearán automáticamente las tablas necesarias y se generará un usuario administrador predeterminado.

### Juego de ejemplo

El proyecto incluye un juego básico llamado **Adivina el Número** en la carpeta `games/adivina-numero`. Si deseas registrarlo manualmente puedes ejecutar el script `db/insert_sample_game.sql`.

### Uso

- **Inicio** (`index.php`): muestra los juegos aprobados con filtros por categoría o edad.
- **Subir Juego** (`upload.php`): opción disponible para usuarios con rol creador o administrador. Permite subir un paquete ZIP que contenga un `index.html`.
- **Administración** (`admin.php`): desde aquí se gestionan juegos y usuarios.
- **Jugar** (`game.php?id=ID`): abre el juego en un *iframe* y permite votar con estrellas.
- **Inicio de Sesión** (`login.php`): diferentes perfiles de estudiante, creador y administrador.

### Roles de Usuario

- **estudiante**: navega y califica juegos.
- **creador**: puede subir nuevos juegos.
- **admin**: aprueba y elimina juegos, además de gestionar cuentas.

### Licencia

Este proyecto se distribuye bajo la licencia MIT. Consulta el archivo `LICENSE` para más detalles.

## Parte didáctica

Esta web se diseñó como apoyo a las asignaturas de programación del profesorado de Informática del CFE, CERP del Suroeste. Los juegos fueron desarrollados en 2025 por estudiantes en los cursos coordinados por el docente Domingo Pérez. El objetivo es brindar un espacio donde compartir prácticas y ejemplos de código junto a materiales lúdicos creados en clase.

Para enriquecer la experiencia se utilizaron distintas herramientas de inteligencia artificial. Entre ellas se experimentó con motores de razonamiento como **Minimax**, asistentes de texto como **Claude** y **OpenAI**, y generadores de imágenes como **Leonardo AI**, **DALL·E** y otras soluciones libres. Estas tecnologías ayudaron tanto a ilustrar los juegos como a analizar estrategias en actividades de programación.

La plataforma sigue abierta a mejoras y a nuevas contribuciones que promuevan la enseñanza de la informática mediante el juego y la experimentación.
