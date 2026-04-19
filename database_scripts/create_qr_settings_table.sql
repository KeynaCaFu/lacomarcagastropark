-- Tabla para guardar la configuración del QR de validación
CREATE TABLE IF NOT EXISTS `qr_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qr_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE COMMENT 'Clave secreta única para el QR',
  `qr_url` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL del QR generada',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Si el QR está activo o no',
  `generated_by` bigint(20) unsigned NOT NULL COMMENT 'ID del administrador que generó el QR',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
  PRIMARY KEY (`id`),
  KEY `generated_by` (`generated_by`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `qr_settings_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `tbuser` (`user_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para registrar logs de generación/actualización del QR
CREATE TABLE IF NOT EXISTS `qr_generation_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qr_setting_id` bigint(20) unsigned NOT NULL COMMENT 'ID del QR en qr_settings',
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Acción realizada: generate, update, download',
  `old_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL COMMENT 'Clave anterior (si fue actualización)',
  `new_key` varchar(255) COLLATE utf8mb4_unicode_ci NULL COMMENT 'Nueva clave generada',
  `admin_id` bigint(20) unsigned NOT NULL COMMENT 'ID del administrador que realizó la acción',
  `admin_ip` varchar(45) COLLATE utf8mb4_unicode_ci NULL COMMENT 'IP del administrador',
  `user_agent` longtext COLLATE utf8mb4_unicode_ci NULL COMMENT 'User Agent del navegador',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del evento',
  PRIMARY KEY (`id`),
  KEY `qr_setting_id` (`qr_setting_id`),
  KEY `admin_id` (`admin_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `qr_generation_logs_ibfk_1` FOREIGN KEY (`qr_setting_id`) REFERENCES `qr_settings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qr_generation_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `tbuser` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
