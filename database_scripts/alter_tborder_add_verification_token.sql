-- Script para agregar campos de verificación de token a la tabla tborder
-- CA2: Token numérico único en formato LCGP-XXXX
-- CA5: Registro de fecha y hora de confirmación

ALTER TABLE `tborder` ADD COLUMN `verification_token` VARCHAR(255) UNIQUE COMMENT 'Token único de verificación en formato LCGP-XXXX';

ALTER TABLE `tborder` ADD COLUMN `confirmed_at` TIMESTAMP NULL COMMENT 'Fecha y hora exacta de generación del token (CA5)';

-- Crear índice para búsquedas rápidas de tokens
CREATE INDEX idx_verification_token ON `tborder`(verification_token);

-- Crear índice para búsquedas por fecha de confirmación
CREATE INDEX idx_confirmed_at ON `tborder`(confirmed_at);

-- Nota: El campo 'status' ya existe en la tabla tborder
-- Estados posibles: 'Pending', 'Preparing', 'Ready', 'Delivered', 'Cancelled'
