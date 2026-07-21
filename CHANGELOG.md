# CHANGELOG

## Fase 2 — v1.2 (2026-07-21)

- Se completó la integración de Google OAuth y la carga de credenciales desde `private/config.php`.
- Se corrigió la ruta de carga en `config/google_oauth.php` para que funcione con la estructura de carpetas `private/` y `public_html` separadas.
- Se incorporó soporte para la configuración `upload_types` en `upload.php`, con prioridad a `private/config.php` y fallback a variables de entorno.
- Se creó `private/config.php.example` como referencia para despliegue seguro fuera de `public_html`.
- Se documentó el uso de `APP_VERSION` en el pie de página y se mantuvo compatibilidad con configuraciones existentes.
- Se actualizó `docs/fases/fase_2.md` con el cierre formal de la fase.

---

Notas:
- La próxima fase (Fase 3) incrementará la versión a v1.3.
