# Fase 4 — Reemplazo de imagen de logo por iconos predefinidos

**Fecha de comienzo:** 2026-09-08  
**Fecha de finalización (estimada):** 2026-09-22  
**Estado:** En progreso  

## Resumen de la fase

Esta fase reemplaza la carga manual de una imagen de logo/previsualización por un selector con iconos predefinidos, para que el usuario que publique un juego ya no tenga que subir una imagen propia del juego. En lugar de eso, se mostrará un desplegable con opciones visuales ya preparadas, que se cargarán desde una carpeta interna del proyecto.

La intención es simplificar el proceso de publicación, mantener una identidad visual uniforme en el catálogo y reducir errores en validaciones de archivos, tamaños y resoluciones.

## Hitos

- **Hito 1 — Preparación de la carpeta de iconos**  
  - Fecha objetivo: 2026-09-08  
  - Responsable: Equipo Fullstack / Diseño  
  - Descripción: Crear la carpeta de almacenamiento de iconos predefinidos y dejar documentado el formato recomendado para los archivos.

- **Hito 2 — Cambio en el formulario de carga**  
  - Fecha objetivo: 2026-09-10  
  - Responsable: Equipo Fullstack  
  - Descripción: Eliminar el campo de subida de imagen para el logo del juego y reemplazarlo por un selector desplegable con iconos predefinidos.

- **Hito 3 — Persistencia y visualización del icono elegido**  
  - Fecha objetivo: 2026-09-14  
  - Responsable: Equipo Backend / Frontend  
  - Descripción: Asegurar que el icono seleccionado quede asociado al juego y se muestre correctamente en el catálogo y en la vista del juego.

- **Hito 4 — Validación y pruebas de integración**  
  - Fecha objetivo: 2026-09-18  
  - Responsable: QA / Equipo Fullstack  
  - Descripción: Revisar que el flujo funcione con distintos juegos, que los iconos se rendericen bien y que no queden errores de carga o referencias rotas.

- **Hito 5 — Cierre de la fase**  
  - Fecha objetivo: 2026-09-22  
  - Responsable: Equipo Fullstack  
  - Descripción: Documentar el resultado final, dejar la carpeta de iconos lista para uso y confirmar la transición hacia la siguiente etapa del proyecto.

## Descripción detallada del plan

### Objetivos

- Eliminar la necesidad de que el usuario suba una imagen/logo propia del juego.
- Usar un selector con iconos predefinidos para mantener uniformidad visual.
- Crear una carpeta de assets dedicada para los iconos del catálogo.
- Mantener el flujo de publicación más simple y menos propenso a errores.
- Asegurar que el icono seleccionado siga mostrándose correctamente en todas las vistas del proyecto.

### Entregables

- Carpeta `images/game-icons/` creada y lista para recibir los iconos predefinidos.
- Documento de referencia para el formato y naming de iconos.
- Formulario de carga actualizado sin campo de archivo de imagen.
- Selector desplegable con opciones predefinidas en `upload.php`.
- Lógica que guarde el icono elegido y lo muestre en el catálogo y en la vista del juego.
- Validación y pruebas finales de la nueva experiencia de publicación.

### Tareas y subtareas (cronograma sugerido)

1. Crear la carpeta de iconos y definir el estándar de archivos.  
   - Crear `images/game-icons/` dentro del proyecto.  
   - Definir si los iconos serán SVG, PNG o WebP.  
   - Documentar el nombre de archivo y cómo se usará en el selector.

2. Actualizar el formulario de subida de juego.  
   - Quitar el bloque de carga de imagen de previsualización.  
   - Agregar un campo tipo `select` con los iconos predefinidos disponibles.  
   - Mantener el resto de los datos del juego sin cambios.

3. Ajustar la lógica de guardado.  
   - Reutilizar o redefinir el campo `preview_image` para guardar el nombre del icono seleccionado.  
   - Validar que el valor exista dentro de la carpeta de iconos.  
   - Evitar dependencias de archivos temporales o validaciones de peso/resolución.

4. Actualizar la visualización del catálogo y de cada juego.  
   - Mostrar el icono predefinido en la tarjeta del juego del inicio.  
   - Reemplazar la ruta antigua por la ruta del icono en la carpeta `images/game-icons/`.  
   - Confirmar que la vista del juego renderiza bien el icono seleccionado.

5. Probar el flujo completo.  
   - Publicar un juego usando un icono predefinido.  
   - Verificar la correcta persistencia en la base de datos.  
   - Revisar que el catálogo y la página de juego muestran el mismo icono.

### Recursos necesarios

- Carpeta `images/game-icons/` dentro del repositorio.  
- Set de iconos predefinidos en formatos consistentes.  
- Acceso a `upload.php` para ajustar el formulario.  
- Base de datos con el campo `preview_image` disponible.  
- Equipo de frontend para revisar la apariencia final en el catálogo.  
- Equipo de backend para ajustar la lógica de carga y persistencia.

### Riesgos y mitigaciones

- Riesgo: El usuario intenta usar un icono no incluido en la carpeta.  
  - Mitigación: Validar el valor recibido y limitar las opciones al listado configurado.

- Riesgo: Los iconos no tienen un formato visual consistente.  
  - Mitigación: Definir un estándar de tamaño y estilo antes de subir los archivos.

- Riesgo: El campo de selección queda incompleto o con nombres distintos a los archivos reales.  
  - Mitigación: Mantener una lista controlada y sincronizada con los nombres de archivo disponibles.

- Riesgo: La vista del catálogo no muestra bien algunos iconos.  
  - Mitigación: Probar en la UI final y ajustar estilos si algún formato necesita una corrección visual.

### Criterios de aceptación

- El formulario de subida ya no exige subir una imagen/logo del juego.  
- El usuario selecciona el icono desde un desplegable con opciones predefinidas.  
- Los iconos se encuentran en `images/game-icons/` y son consumidos desde esa carpeta.  
- El catálogo y la vista detallada del juego muestran correctamente el icono elegido.  
- La publicación del juego funciona sin errores de validación relacionados con imagen local.  
- La documentación de la fase queda actualizada y la carpeta de iconos queda lista para uso.

---

Nota: Esta fase reemplaza el flujo de “subir una imagen de logo” por un selector de iconos predefinidos. Los iconos reales se cargarán en la carpeta `images/game-icons/` por el equipo responsable.

## Variables de entorno

Anota aquí las variables de entorno relacionadas con la fase y su uso. Se recomienda definirlas en el servidor o usar un archivo de configuración para valores por defecto en desarrollo.

- `GAME_ICONS_DIR`: ruta relativa o absoluta de la carpeta que contiene los iconos predefinidos disponibles para los juegos.  
  - Valor sugerido en este proyecto: `images/game-icons/`
- `UPLOAD_TYPES`: controla los tipos de subida aceptados por `upload.php`. Valores: `repo`, `site`, `both`.
- `APP_VERSION`: versión actual de la fase/entrega (ej: `1.2`). Se muestra en el pie de página.

Mantener esta sección actualizada cuando se introduzcan nuevas variables o cambios de comportamiento.
