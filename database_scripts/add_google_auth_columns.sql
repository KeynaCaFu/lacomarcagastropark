-- Script para agregar columnas de Google OAuth a la tabla tbuser
-- Ejecutar en phpMyAdmin o MySQL CLI


-- =========================================
-- COLUMNAS PARA VALIDACIÓN DE SESIÓN GOOGLE
-- =========================================

-- Columna para registrar cuándo inició la sesión de Google
ALTER TABLE tbuser ADD COLUMN google_session_start_time TIMESTAMP NULL DEFAULT NULL AFTER avatar;

-- Columna para registrar la última actividad en sesión de Google
ALTER TABLE tbuser ADD COLUMN google_session_last_activity TIMESTAMP NULL DEFAULT NULL AFTER google_session_start_time;

-- =========================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- =========================================

-- Crear índice en provider_id para búsquedas rápidas
ALTER TABLE tbuser ADD INDEX idx_provider_id (provider_id);

-- Crear índice combinado en provider + provider_id
ALTER TABLE tbuser ADD INDEX idx_provider_provider_id (provider, provider_id);

-- Crear índice en google_session_start_time para queries de expiración
ALTER TABLE tbuser ADD INDEX idx_google_session_start_time (google_session_start_time);

-- =========================================
-- VERIFICACIÓN
-- =========================================

-- Verificar que las columnas se agregaron correctamente
DESCRIBE tbuser;
