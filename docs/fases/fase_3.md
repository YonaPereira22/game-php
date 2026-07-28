# Fase 3 — Plan y Hitos

**Fecha de comienzo:** 2026-07-28  
**Fecha de finalización (estimada):** 2026-08-31  
**Estado:** Planificada  

## Resumen de la fase

Esta fase se enfoca en enriquecer los juegos publicados con documentación integrada, mejorar la experiencia de creación y agregar controles de visualización de pantalla completa.

## Hitos

- **Hito 1 — README por juego**  
  - Fecha objetivo: 2026-08-07  
  - Responsable: Equipo Fullstack  
  - Descripción: Permitir que cada juego tenga un README adjunto que describa cómo jugar, de qué trata y los contenidos trabajados.

- **Hito 2 — Asignación de README en el formulario**  
  - Fecha objetivo: 2026-08-12  
  - Responsable: Equipo Fullstack  
  - Descripción: Agregar en el formulario de subida la opción de asociar el README al juego y guardarlo junto a los datos del juego.

- **Hito 3 — Visor de README dentro del juego**  
  - Fecha objetivo: 2026-08-16  
  - Responsable: Equipo Frontend  
  - Descripción: Implementar un visor que muestre el README del juego dentro de la interfaz cuando se selecciona o se reproduce el juego.

- **Hito 4 — Imagen de previsualización del juego**  
  - Fecha objetivo: 2026-08-20  
  - Responsable: Equipo Fullstack / Diseño  
  - Descripción: Añadir campo obligatorio para subir imagen de previsualización en el formulario de juego, con validación de tamaño y resolución mínima.

- **Hito 5 — Pantalla fija y botón de pantalla completa**  
  - Fecha objetivo: 2026-08-27  
  - Responsable: Equipo Frontend  
  - Descripción: Modificar la vista de juego para que el menú no scrollee y agregar un botón de pantalla completa durante la reproducción.

## Descripción detallada del plan

### Objetivos

- Agregar documentación interna por juego mediante README.  
- Hacer visible el README en el juego con un visor embebido.  
- Permitir subir una imagen de previsualización por juego en el formulario.  
- Mejorar la experiencia de juego con interfaz fija y modo pantalla completa.

### Entregables

- Formulario de creación de juegos con soporte para README y preview image.  
- Lógica de almacenamiento y asociación de README en la base de datos.  
- Visor de README dentro del juego.  
- Validación de imagen de previsualización con límite de MB y resolución mínima.  
- Interfaz de juego sin scroll y botón de pantalla completa.

### Tareas y subtareas (cronograma sugerido)

1. Definir esquema de almacenamiento para README y preview image.  
   - Revisar si se usa `games/` para guardar archivos adicionales.  
   - Agregar campos en la base de datos si es necesario.
2. Actualizar `upload.php` y formulario de publicación.  
   - Añadir campo para README (archivo markdown o texto).  
   - Añadir campo para imagen de previsualización con validación de peso y tamaño.  
3. Implementar la carga y asociación de README en la base de datos.  
   - Guardar archivo o contenido en el directorio del juego.  
   - Registrar ruta/URL en la tabla `games` o en tabla relacionada.  
4. Crear visor de README dentro del juego.  
   - Renderizar Markdown como HTML.  
   - Agregar acceso desde la página del juego.
5. Ajustar la vista del juego para mantener el contenido fijo.  
   - Evitar scroll del menú principal dentro de la página de juego.  
   - Agregar botón de pantalla completa.
6. Probar carga, lectura y visualización de README e imagen de preview.  
   - Verificar compatibilidad con varios juegos y resoluciones.

### Recursos necesarios

- Equipo de frontend para el visor y pantalla completa.  
- Equipo de backend para la carga y validación de archivos.  
- Diseñador para definir la vista de la previsualización.  
- Entorno de pruebas para validar tamaño de archivos y visualización.

### Riesgos y mitigaciones

- Riesgo: archivos demasiado grandes o formatos no válidos.  
  - Mitigación: validar peso y extensiones en el servidor y el cliente, mostrar mensajes claros.  
- Riesgo: README mal formado o inseguro.  
  - Mitigación: sanitizar Markdown antes de renderizar y limitar recursos externos.
- Riesgo: el botón de pantalla completa puede no funcionar en algunos navegadores.  
  - Mitigación: implementar fallback y mensaje de compatibilidad.

### Criterios de aceptación

- README asociado a cada juego y visible desde la interfaz.  
- Imagen de previsualización obligatoria cargada en el formulario y validada.  
- Página de juego sin scroll y con botón de pantalla completa funcional.  
- Documentación de la fase lista y aprobada.

---

Nota: Actualiza las fechas, responsables y detalles según avance el trabajo.
