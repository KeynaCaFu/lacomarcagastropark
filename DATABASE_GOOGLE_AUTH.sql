-- ============================================================================
-- SCRIPT SQL PARA CONFIGURAR GOOGLE AUTH EN LA COMARCAGASTROPARK
-- ============================================================================

-- ============================================================================
-- 1. AGREGAR COLUMNAS A LA TABLA TBUSER (si no existen)
-- ============================================================================

-- Si la tabla ya existe, ejecutar:
ALTER TABLE tbuser 
ADD COLUMN provider VARCHAR(255) NULL COMMENT 'Google, Facebook, etc.',
ADD COLUMN provider_id VARCHAR(255) NULL COMMENT 'ID del usuario en el proveedor',
ADD COLUMN avatar VARCHAR(255) NULL COMMENT 'URL de la foto de perfil';

-- Opcional: Crear índices para mejorar búsquedas
ALTER TABLE tbuser ADD INDEX idx_provider_id (provider_id);
ALTER TABLE tbuser ADD INDEX idx_email (email);

-- ============================================================================
-- 2. VERIFICAR QUE EXISTE LA TABLA TBROLE
-- ============================================================================

-- Ver estructura de tbrole (ejemplo):
-- CREATE TABLE tbrole (
--     role_id INT PRIMARY KEY AUTO_INCREMENT,
--     role_type VARCHAR(50) NOT NULL UNIQUE,
--     description VARCHAR(255)
-- );

-- ============================================================================
-- 3. INSERTAR ROLES BÁSICOS (si no existen)
-- ============================================================================

INSERT IGNORE INTO tbrole (role_type, description) VALUES
('Cliente', 'Usuario cliente estándar'),
('Gerente', 'Gerente de local/tienda'),
('Admin', 'Administrador global'),
('SuperAdmin', 'Super administrador del sistema');

-- ============================================================================
-- 4. CREAR UN USUARIO DE PRUEBA CON GOOGLE AUTH (opcional)
-- ============================================================================

-- Ejemplo de un usuario creado por Google Auth:
INSERT INTO tbuser (
    full_name,
    email,
    phone,
    password,
    role_id,
    status,
    provider,
    provider_id,
    avatar
) VALUES (
    'Juan Pérez Google',
    'juan.google@example.com',
    NULL,
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/iu',  -- bcrypt random password
    (SELECT role_id FROM tbrole WHERE role_type = 'Cliente' LIMIT 1),
    'Active',
    'google',
    '123456789012345678901',  -- ID de Google
    'https://lh3.googleusercontent.com/a/ejemplo'
);

-- ============================================================================
-- 5. VERIFICAR INTEGRIDAD DE DATOS
-- ============================================================================

-- Ver roles disponibles:
SELECT * FROM tbrole;

-- Ver usuarios con Google Auth:
SELECT user_id, full_name, email, provider, provider_id, avatar 
FROM tbuser 
WHERE provider = 'google';

-- Ver estructura de tbuser:
DESCRIBE tbuser;

-- ============================================================================
-- 6. CONSULTAS ÚTILES PARA MANTENIMIENTO
-- ============================================================================

-- Listar todos los usuarios autenticados con Google:
SELECT 
    user_id,
    full_name,
    email,
    provider,
    avatar,
    r.role_type as rol
FROM tbuser u
LEFT JOIN tbrole r ON u.role_id = r.role_id
WHERE u.provider = 'google'
ORDER BY user_id DESC;

-- Buscar usuario por email de Google:
SELECT * FROM tbuser WHERE email = 'user@gmail.com' AND provider = 'google';

-- Buscar usuario por provider_id:
SELECT * FROM tbuser WHERE provider_id = '123456789';

-- Contar usuarios por proveedor de autenticación:
SELECT 
    IFNULL(provider, 'local') as provider,
    COUNT(*) as cantidad
FROM tbuser
GROUP BY provider;

-- Actualizar avatar de un usuario:
UPDATE tbuser 
SET avatar = 'https://nueva-foto.jpg' 
WHERE user_id = 1;

-- Desasociar Google de un usuario (sin borrar):
UPDATE tbuser 
SET provider = NULL, provider_id = NULL, avatar = NULL 
WHERE user_id = 1;

-- ============================================================================
-- 7. AUDITORÍA Y LOGS (opcional)
-- ============================================================================

-- Si deseas registrar intentos de login con Google:
-- CREATE TABLE login_logs (
--     log_id INT PRIMARY KEY AUTO_INCREMENT,
--     user_id INT,
--     provider VARCHAR(50),
--     ip_address VARCHAR(45),
--     user_agent VARCHAR(500),
--     login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (user_id) REFERENCES tbuser(user_id)
-- );

-- ============================================================================
-- NOTAS IMPORTANTES
-- ============================================================================

/*
1. Los campos nuevos (provider, provider_id, avatar) pueden ser NULL
   para usuarios locales (no Google Auth)

2. El campo provider puede tener valores: 'google', 'facebook', etc.
   si implementas múltiples proveedores en el futuro

3. El field provider_id almacena el ID único de Google para ese usuario

4. El avatar puede ser NULL si se desea usar una imagen por defecto

5. Al crear un usuario por Google, la contraseña es aleatoria y no se usa

6. La role_id de usuarios Google es automáticamente 'Cliente'

7. El status por defecto es 'Active' para usuarios nuevos de Google
*/
