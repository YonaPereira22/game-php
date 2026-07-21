
# Fase 2 — Plan y Hitos

**Fecha de comienzo:** 2026-05-01  
**Fecha de finalización:** 2026-07-21  
**Estado:** Completada  

## Resumen de la fase

Esta fase concluye el desarrollo de la integración OAuth, la gestión de configuración privada fuera de `public_html`, y la definición de la lógica de subida de juegos con soporte de tipos configurables.

Breve descripción del objetivo general de la fase: implementar las funcionalidades centrales, migrar datos necesarios y preparar la preparación para la integración con OAuth y pruebas de usuario.

## Hitos

Cada hito debe incluir: nombre, fecha objetivo, responsable y breve descripción.

- **Hito 1 — Estructura de la base de datos completa**  
	- Fecha objetivo: 2026-05-15  
	- Responsable: Equipo Backend  
	- Descripción: Crear y validar migraciones, incluir índices y relaciones necesarias.

- **Hito 2 — Integración OAuth con Google**  
	- Fecha objetivo: 2026-06-05  
	- Responsable: Equipo Backend  
	- Descripción: Implementar `google_login.php`, `google_callback.php` y configuración en `config/google_oauth.php`.

- **Hito 3 — Subida y gestión de juegos**  
	- Fecha objetivo: 2026-06-20  
	- Responsable: Equipo Fullstack  
	- Descripción: Permitir subir juegos, validar archivos y generar miniaturas en `images/game-thumbnails/`.

- **Hito 4 — UI/UX y vista previa**  
	- Fecha objetivo: 2026-07-05  
	- Responsable: Equipo Frontend  
	- Descripción: Revisar estilos (`css/style.css`, `css/style-preview.css`) y funcionalidad de `preview.php`.

- **Hito 5 — Pruebas y despliegue en staging**  
	- Fecha objetivo: 2026-07-20  
	- Responsable: QA / DevOps  
	- Descripción: Ejecutar pruebas de integración, preparar entorno de staging y checklist de despliegue.

## Descripción detallada del plan

### Objetivos

- Completar la estructura de datos y migraciones.  
- Implementar autenticación vía Google.  
- Habilitar gestión de juegos (subida, edición, vista previa).  
- Asegurar experiencia de usuario consistente y accesible.  
- Preparar la entrega para pruebas con usuarios y despliegue a staging.

### Entregables

- Migraciones y scripts SQL actualizados en `db/migrations/`.  
- Rutas y páginas OAuth funcionando (`google_login.php`, `google_callback.php`).  
- Páginas para subir/editar juegos (`upload.php`, `game.php`).  
- Estilos y scripts actualizados en `css/` y `js/`.  
- Documentación de la fase y checklist de pruebas (`docs/fases/fase_2.md`).

### Tareas y subtareas (cronograma sugerido)

1. Revisar y completar migraciones (2 días).  
	 - Ejecutar `db/migrate` en entorno local.  
2. Configurar OAuth y pruebas locales (4 días).  
	 - Revisar `config/google_oauth.php` y credenciales.  
3. Implementar subida de juegos y validaciones (5 días).  
	 - Validar tipos MIME y tamaños, generar miniaturas.  
4. Mejorar UI/UX y hacer revisiones de accesibilidad (4 días).  
5. Pruebas de integración y corrección de fallos (5 días).  
6. Preparar staging y checklist de despliegue (3 días).

### Recursos necesarios

- Acceso a la base de datos de desarrollo.  
- Credenciales OAuth de Google (dev).  
- Entorno de staging para despliegues y pruebas.  
- Equipo de QA disponible para pruebas de usuario.

### Riesgos y mitigaciones

- Riesgo: Retrasos en la obtención de credenciales OAuth.  
	- Mitigación: Usar cuentas de prueba y simuladores locales mientras tanto.
- Riesgo: Migraciones que rompen datos existentes.  
	- Mitigación: Copias de seguridad y pruebas en entorno local antes de aplicar a staging.

### Criterios de aceptación

- Todas las migraciones aplicadas sin errores en entorno de staging.  
- Flujo OAuth probado con cuentas de Google.  
- Subida de juegos validada y miniaturas generadas correctamente.  
- Checklist de QA completado y aprobaciones registradas.  
- Configuración privada cargada desde `private/config.php` fuera de `public_html`.
- Documentación actualizada para la transición a Fase 3.

## Cierre de la fase

- Se finalizó la integración de Google OAuth y se corrigió la carga de credenciales desde `private/config.php`.
- Se implementó la propiedad `upload_types` y se priorizó la configuración privada con fallback a variables de entorno para `upload.php`.
- Se creó y documentó `private/config.php.example` para la configuración privada, junto con los ejemplos de rutas y carga de bootstrap.
- Se dejó documentado el uso de `APP_VERSION` y la carga de configuración en `includes/footer.php`.
- Se actualizó `CHANGELOG.md` y se cerró el documento de fase.

---

Nota: Actualiza las fechas, responsables y detalles según avance el trabajo.

