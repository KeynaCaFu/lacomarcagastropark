-- Insertar roles si no existen
INSERT INTO `tbrole` (`role_id`, `role_type`, `permissions_list`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin_global,manage_all', '2025-12-03 00:00:00', '2025-12-03 00:00:00'),
(2, 'Manager', 'admin_local,manage_local', '2025-12-03 00:00:00', '2025-12-03 00:00:00')
ON DUPLICATE KEY UPDATE `role_type`=`role_type`;

-- Insertar usuario administrador global
INSERT INTO `tbuser` (`user_id`, `full_name`, `phone`, `email`, `password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrator Principal', '8888-0000', 'admin@gmail.com', '$2y$12$4YHqvWDj9s0sTpJt9ZFM3.4J5wEOKB3.QjX5zJH8QK8N9Q5Z5V6Wy', 1, 'Active', '2025-12-03 00:00:00', '2025-12-03 00:00:00')
ON DUPLICATE KEY UPDATE `email`=`email`;

-- Insertar usuarios gerentes si no existen
INSERT INTO `tbuser` (`user_id`, `full_name`, `phone`, `email`, `password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Gerente Punta Mona', '8888-0001', 'gerente.puntamona@gmail.com', '$2y$12$4YHqvWDj9s0sTpJt9ZFM3.4J5wEOKB3.QjX5zJH8QK8N9Q5Z5V6Wy', 2, 'Active', '2025-12-03 00:00:00', '2025-12-03 00:00:00'),
(3, 'Gerente El Sevichito', '8888-0002', 'gerente.sevichito@gmail.com', '$2y$12$4YHqvWDj9s0sTpJt9ZFM3.4J5wEOKB3.QjX5zJH8QK8N9Q5Z5V6Wy', 2, 'Active', '2025-12-03 00:00:00', '2025-12-03 00:00:00')
ON DUPLICATE KEY UPDATE `email`=`email`;

-- Asignar locales a gerentes
INSERT INTO `tbuser_local` (`user_id`, `local_id`, `created_at`, `updated_at`) VALUES
(2, 1, '2025-12-03 00:00:00', '2025-12-03 00:00:00'),
(3, 2, '2025-12-03 00:00:00', '2025-12-03 00:00:00')
ON DUPLICATE KEY UPDATE `user_id`=`user_id`;
