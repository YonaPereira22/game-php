-- 02_modificar_users_oauth.sql
-- Ajusta users para login tradicional + Google OAuth y datos de auditoria.

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS email VARCHAR(100) NULL AFTER username,
  ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL AFTER password,
  ADD COLUMN IF NOT EXISTS google_picture VARCHAR(500) NULL AFTER google_id,
  ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER approved,
  ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL DEFAULT NULL AFTER created_at;

ALTER TABLE users
  MODIFY COLUMN password VARCHAR(255) NULL,
  MODIFY COLUMN email VARCHAR(100) NULL;

ALTER TABLE users
  ADD UNIQUE KEY IF NOT EXISTS uq_users_email (email),
  ADD UNIQUE KEY IF NOT EXISTS uq_users_google_id (google_id);
