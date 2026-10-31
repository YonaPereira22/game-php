# Iconos de juegos

Esta carpeta se usa para almacenar los iconos predefinidos que aparecerán en el desplegable del formulario de carga de juegos.

## Reglas recomendadas

- Usar archivos con nombres claros y consistentes, por ejemplo: `astronomía.svg`, `matematicas.png`, `robotica.webp`.
- Preferir formatos SVG o PNG para mejor escalabilidad y claridad visual.
- Mantener un estilo visual uniforme entre todos los iconos.
- Preparar los iconos rasterizados en un lienzo cuadrado de `200 × 200` píxeles.
- Recortar márgenes transparentes excesivos y centrar la ilustración, conservando su proporción para evitar que se vea más pequeña o deformada que las demás.
- Para SVG, usar un `viewBox` cuadrado y una composición visual equivalente.
- El nombre del archivo debe coincidir con la opción que se muestre en el selector del formulario.

## Uso

Los iconos de esta carpeta serán listados en el desplegable de `upload.php` y luego se guardarán como referencia en el campo `preview_image` del juego.
