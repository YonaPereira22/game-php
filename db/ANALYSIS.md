# An\u00e1lisis de la Base de Datos de Juegos

Este repositorio almacena una plataforma en PHP para compartir juegos educativos basados en HTML5. A continuaci\u00f3n se describe la estructura propuesta de la base de datos.

## Tablas

### `games`
Contiene la informaci\u00f3n de cada juego subido por los usuarios.

- `id`: clave primaria autoincremental.
- `title`: t\u00edtulo del juego.
- `description`: descripci\u00f3n breve del juego.
- `author`: nombre del autor o creador.
- `folder_name`: carpeta en la que se almacena el juego en el servidor. Debe ser \u00fanica.
- `category`: categor\u00eda a la que pertenece (ej. matem\u00e1ticas, ciencias, etc.).
- `age_group`: grupo de edad recomendado.
- `created_at`: fecha de carga.
- `approved`: indica si el juego ya fue aprobado por un administrador.
- `total_votes` y `average_rating`: n\u00famero total de votos y promedio de valoraci\u00f3n (1--5 estrellas).

### `votes`
Registra las valoraciones hechas por los usuarios.

- `id`: clave primaria autoincremental.
- `game_id`: referencia al juego votado.
- `user_ip`: IP del usuario que vota (se usa para evitar votos duplicados).
- `rating`: valor de 1 a 5 estrellas.
- `voted_at`: fecha en la que se emiti\u00f3 el voto.

Se define una clave for\u00e1nea sobre `game_id` que elimina los votos asociados si un juego se borra, y una restricci\u00f3n \u00fanica para impedir que un mismo usuario vote m\u00e1s de una vez.

## Consideraciones

- El script `db/schema.sql` crea la base de datos y las dos tablas necesarias. Al ejecutarlo en un servidor MySQL o MariaDB se obtendr\u00e1 una estructura lista para utilizar con la aplicaci\u00f3n.
- La aplicaci\u00f3n incluye tambi\u00e9n c\u00f3digo PHP (`config/database.php`) que crea las tablas autom\u00e1ticamente si no existen, pero se proporciona este script para administradores que prefieran configurar la base manualmente.
