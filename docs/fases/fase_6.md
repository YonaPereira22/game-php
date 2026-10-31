# Fase 6 — Asistentes para recomendar juegos

**Fecha de comienzo:** 2026-10-31
**Fecha de finalización estimada:** Por definir
**Estado:** En progreso

## Resumen de la fase

Incorporar en la portada una pareja de personajes animados en la esquina inferior derecha del área gráfica del hero, con un cartel pequeño encima. Tanto el cartel como los personajes abren un chat de recomendaciones. La primera versión consulta únicamente los juegos aprobados del catálogo, utiliza el tema y la edad indicados para ordenar sugerencias y no guarda la conversación ni la envía a servicios externos. La interfaz se identifica como asistentes, pero informa que esta versión inicial no usa IA generativa.

## Hitos

- **Hito 1 — Personajes e invitación en la portada**
  - Fecha objetivo: 2026-10-31
  - Responsable: Equipo de desarrollo
  - Descripción: Preparar los recursos transparentes y la animación de tres cuadros; integrar un cartel compacto sobre los personajes en el rincón inferior derecho del hero.

- **Hito 2 — Chat y recomendaciones del catálogo**
  - Fecha objetivo: 2026-10-31
  - Responsable: Equipo de desarrollo
  - Descripción: Implementar un chat accesible que busca juegos aprobados por tema y edad, y presenta enlaces a las recomendaciones.

- **Hito 3 — Validación y documentación**
  - Fecha objetivo: 2026-10-31
  - Responsable: Equipo de desarrollo
  - Descripción: Comprobar la sintaxis PHP, las dimensiones de los recursos y el comportamiento responsive; documentar alcance y limitaciones.

## Descripción detallada del plan

### Objetivos

- Facilitar la exploración del catálogo desde una interfaz conversacional.
- Mantener los personajes en el rincón inferior derecho del hero, sin superponerlos al texto principal ni al catálogo.
- Priorizar juegos aprobados y considerar tema, edad y popularidad al recomendar.
- Evitar recolectar datos personales o compartir conversaciones con servicios externos.
- Dejar explícita la diferencia entre esta recomendación basada en reglas y una futura IA generativa.

### Entregables

- Imágenes optimizadas y transparentes de los asistentes en `images/assistants/`.
- Cartel compacto y personajes animados integrados en el área gráfica del hero, ambos interactivos.
- Diálogo adaptable a dispositivos móviles.
- Interfaz JavaScript de chat en `js/ai-assistant.js`.
- Endpoint `recomendaciones.php` para ordenar y devolver juegos aprobados.
- Estilos del banner y del chat en `css/style.css`.
- Esta documentación de la fase.

### Tareas y subtareas (cronograma sugerido)

1. Preparar personajes.
   - Redimensionar el retrato y los tres cuadros de animación para reducir el peso de descarga.
   - Mantener la transparencia y las proporciones.

2. Construir la experiencia en la portada.
   - Ubicar un cartel compacto sobre los personajes en la esquina inferior derecha del área gráfica del hero.
   - Permitir abrir el chat desde el cartel o desde los personajes.
   - Abrir un chat con mensajes introductorios y ejemplos de consultas.
   - Respetar la preferencia del navegador por reducir movimiento.

3. Implementar recomendaciones.
   - Recibir consultas de hasta 500 caracteres.
   - Comparar palabras del mensaje con materia, título y descripción.
   - Priorizar coincidencias de edad, juegos aprobados y valoraciones.
   - Devolver como máximo tres resultados y construir enlaces con sus identificadores.

4. Validar y cerrar la fase.
   - Revisar sintaxis PHP, tamaños de las imágenes y errores del diff.
   - Probar consultas por materia, edad, todas las edades y catálogo vacío.
   - Actualizar el estado de la fase al completar las pruebas en el entorno de despliegue.

### Recursos necesarios

- PHP con PDO y conexión a la base de datos de juegos.
- Juegos aprobados con título, descripción, categoría y grupo de edad.
- Navegador con soporte para `<dialog>` y `fetch`.
- Retrato y hoja de sprites de los personajes.

### Riesgos y mitigaciones

- Riesgo: La búsqueda por palabras no interpreta sinónimos ni mantiene contexto como un modelo generativo.
  - Mitigación: Mostrar los criterios usados, permitir repetir la consulta y considerar IA generativa como una etapa opcional.

- Riesgo: No hay juegos que coincidan o el catálogo está vacío.
  - Mitigación: Mostrar un mensaje explícito y no incluir juegos pendientes de aprobación.

- Riesgo: Las consultas públicas elevan la carga del servidor.
  - Mitigación: Limitar el tamaño del mensaje y devolver como máximo tres recomendaciones.

- Riesgo: La animación distrae a usuarios sensibles al movimiento.
  - Mitigación: Desactivar la animación cuando el navegador activa `prefers-reduced-motion`.

### Criterios de aceptación

- Los personajes aparecen en la esquina inferior derecha del área gráfica del hero y el cartel se ubica encima de ellos.
- Tanto el cartel como los personajes abren el diálogo de chat.
- Los personajes se animan con sus cuadros y conservan la transparencia.
- El diálogo permite escribir consultas o elegir ejemplos y muestra enlaces a juegos aprobados.
- Las recomendaciones consideran tema y edad cuando el mensaje contiene esos datos.
- El endpoint valida el método y el tamaño de entrada, y devuelve JSON con errores controlados.
- La conversación no se persiste ni se envía a un proveedor externo.
- La interfaz informa claramente que todavía no usa IA generativa.
- El diseño se adapta a pantallas pequeñas y a la preferencia de movimiento reducido.

---

## Variables de entorno

Esta fase no agrega variables de entorno. La búsqueda utiliza el catálogo de la base de datos existente y no requiere una clave de proveedor de IA.
