# 🔐 Guía de Configuración: Login, Registro y Google OAuth

## 📋 Resumen de Cambios

Se ha implementado un sistema completo de autenticación con:
- ✅ **Registro de usuarios** (register.php)
- ✅ **Login tradicional** (login.php)
- ✅ **Autenticación con Google** (Google OAuth 2.0)
- ✅ **Cierre de sesión seguro** (logout.php)

---

## 🚀 Paso a Paso para Configurar Google OAuth

### 1. Crear Proyecto en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Espera a que el proyecto se cree

### 2. Habilitar la API de Google+

1. En el menú lateral, ve a **"APIs y servicios"** → **"Biblioteca"**
2. Busca **"Google+ API"** o **"Google Identity"**
3. Haz clic en ella y luego en **"Habilitar"**

### 3. Crear Credenciales OAuth

1. Ve a **"APIs y servicios"** → **"Credenciales"**
2. Haz clic en **"+ Crear credenciales"** → **"ID de cliente OAuth"**
3. Si te pide, configura la pantalla de consentimiento primero:
   - Selecciona "Externo"
   - Completa los datos básicos
   - En "Scopes", agrega: `openid`, `email`, `profile`

### 4. Configurar la Aplicación OAuth

1. Selecciona **"Aplicación web"** como tipo de aplicación
2. Dale un nombre (ej: "Game Platform")
3. En **"URIs de redirección autorizados"**, agrega:
   - **Para desarrollo local:**
     ```
     http://localhost/game/game-php/google_callback.php
     ```
   - **Para producción:**
     ```
     https://tudominio.com/google_callback.php
     ```
4. Haz clic en **"Crear"**

### 5. Copiar Credenciales

1. Se mostrará una ventana con:
   - **ID de cliente** (Client ID)
   - **Secreto del cliente** (Client Secret)
2. Copia ambos valores

### 6. Configurar en tu Aplicación

1. Abre `config/google_oauth.php`
2. Reemplaza:
   ```php
   define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE.apps.googleusercontent.com');
   define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
   ```
   Con tus credenciales reales

3. Ejemplo:
   ```php
   define('GOOGLE_CLIENT_ID', '123456789-abcdefghijk.apps.googleusercontent.com');
   define('GOOGLE_CLIENT_SECRET', 'GOCSPX-1234567890abcdef');
   ```

---

## 🗄️ Actualizar Base de Datos

### Opción 1: Usando el Script de Migración (Recomendado)

1. Abre una terminal en la carpeta del proyecto
2. Ejecuta:
   ```bash
   php db/migrate_users_table.php
   ```

### Opción 2: Ejecutar SQL Manualmente

1. Abre phpMyAdmin o tu cliente MySQL
2. Selecciona la base de datos `game`
3. Ejecuta el archivo: `db/schema.sql`

O ejecuta manualmente estas sentencias:

```sql
ALTER TABLE users MODIFY password VARCHAR(255);
ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE NOT NULL AFTER username;
ALTER TABLE users ADD COLUMN google_id VARCHAR(255) UNIQUE AFTER password;
ALTER TABLE users ADD COLUMN google_picture VARCHAR(500) AFTER google_id;
ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL;
```

---

## 📁 Archivos Nuevos y Modificados

### Nuevos Archivos:
- `register.php` - Página de registro
- `google_login.php` - Inicia flujo OAuth con Google
- `google_callback.php` - Maneja callback de Google
- `config/google_oauth.php` - Configuración de credenciales
- `db/migrate_users_table.php` - Script de migración de BD

### Archivos Modificados:
- `db/schema.sql` - Schema actualizado
- `login.php` - Mejorado con Google OAuth
- `logout.php` - Más seguro
- `db/schema.sql` - Agregadas columnas para Google

---

## 🔑 Flujo de Autenticación

### Registro:
```
Usuario → register.php → Validación → BD → Redirige a login
```

### Login Tradicional:
```
Usuario → login.php → Verifica credenciales → Crea sesión → index.php
```

### Login con Google:
```
Usuario hace clic "Google" → google_login.php → Google OAuth → google_callback.php → Crea usuario o sesión → index.php
```

---

## 🧪 Pruebas

### 1. Probar Registro
1. Ve a `http://localhost/game/game-php/register.php`
2. Completa el formulario
3. Deberías ser redirigido a login después de registrarte

### 2. Probar Login Tradicional
1. Ve a `http://localhost/game/game-php/login.php`
2. Usa las credenciales que creaste
3. Deberías ir a `index.php` con sesión activa

### 3. Probar Google OAuth
1. Ve a `http://localhost/game/game-php/login.php`
2. Haz clic en "Inicia sesión con Google"
3. Completa el flujo de Google
4. Deberías ir a `index.php` con sesión activa

---

## 🐛 Solución de Problemas

### "Error de seguridad: estado inválido"
- **Causa**: Las sesiones no se están guardando correctamente
- **Solución**: Verifica que `php.ini` tenga:
  ```ini
  session.save_path = /tmp  (o tu ruta de sesiones)
  session.use_cookies = On
  ```

### "Las credenciales de Google OAuth no están configuradas"
- **Causa**: No has configurado las credenciales en `config/google_oauth.php`
- **Solución**: Sigue el paso 6 de "Configurar en tu Aplicación"

### "Error de redirección URI"
- **Causa**: La URL en Google Cloud Console no coincide
- **Solución**: Verifica que sea exactamente:
  ```
  http://localhost/game/game-php/google_callback.php
  ```

### "Email ya existe"
- **Causa**: Intentaste crear una cuenta con email registrado
- **Solución**: Usa otro email o recupera tu contraseña

### Usuario "pendiente de aprobación"
- **Causa**: El usuario fue creado pero no fue aprobado por un admin
- **Solución**: 
  - Para creadores: Espera a que un administrador lo apruebe
  - Para estudiantes: Se aprueban automáticamente

---

## 📱 Características del Sistema

### Tipos de Usuarios:
1. **Estudiante** (student)
   - Puede jugar y votar
   - Se aprueba automáticamente

2. **Creador** (creator)
   - Puede subir juegos
   - Requiere aprobación del admin

3. **Administrador** (admin)
   - Controla todo
   - Aprueba usuarios y juegos

### Campos de Usuario:
- `id` - ID único
- `username` - Nombre de usuario
- `email` - Email único
- `password` - Contraseña (hasheada con bcrypt)
- `google_id` - ID de Google (solo si usa OAuth)
- `google_picture` - Foto de perfil de Google
- `role` - admin, creator o student
- `approved` - Si está aprobado
- `created_at` - Fecha de creación
- `last_login` - Último acceso

---

## 🔒 Seguridad

- ✅ Contraseñas hasheadas con bcrypt
- ✅ CSRF tokens para OAuth
- ✅ Validación de entrada
- ✅ Sesiones seguras
- ✅ Sanitización HTML
- ✅ HTTPS recomendado en producción

---

## 📞 Soporte

Si tienes problemas:
1. Revisa la consola del navegador (F12)
2. Verifica los logs de PHP en `php_errors.log`
3. Asegúrate de que PHP tiene las extensiones: `curl`, `pdo_mysql`

