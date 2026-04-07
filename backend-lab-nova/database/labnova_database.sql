-- ============================================================
-- LabNova - Script de Creación de Base de Datos
-- Base de datos : backend_lab_nova
-- Motor        : MySQL 8.0+
-- Charset      : utf8mb4 / utf8mb4_unicode_ci
-- Generado     : 2026-03-31
-- ============================================================
--
-- INSTRUCCIONES DE USO:
--   Opción A (recomendada con Laravel):
--     php artisan migrate:fresh --seed
--
--   Opción B (este script SQL):
--     1. Ejecutar este archivo en MySQL/DBeaver/phpMyAdmin
--     2. Para contraseñas correctas, ejecutar después:
--        php artisan db:seed --class=UserSeeder
--
--   ADVERTENCIA: Los hashes de contraseña en la sección de
--   usuarios son PLACEHOLDERS (hash de 'password').
--   Para generar el hash correcto de 'Password123!' ejecute:
--     php -r "echo password_hash('Password123!', PASSWORD_BCRYPT, ['cost'=>12]);"
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ============================================================
-- CREAR Y SELECCIONAR LA BASE DE DATOS
-- ============================================================

CREATE DATABASE IF NOT EXISTS `backend_lab_nova`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `backend_lab_nova`;

-- ============================================================
-- TABLAS DEL SISTEMA (Laravel Framework)
-- ============================================================

-- Tabla: cache
CREATE TABLE IF NOT EXISTS `cache` (
    `key`        VARCHAR(255) NOT NULL,
    `value`      MEDIUMTEXT   NOT NULL,
    `expiration` BIGINT       NOT NULL,
    PRIMARY KEY (`key`),
    INDEX `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key`        VARCHAR(255) NOT NULL,
    `owner`      VARCHAR(255) NOT NULL,
    `expiration` BIGINT       NOT NULL,
    PRIMARY KEY (`key`),
    INDEX `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: jobs
CREATE TABLE IF NOT EXISTS `jobs` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue`        VARCHAR(255)    NOT NULL,
    `payload`      LONGTEXT        NOT NULL,
    `attempts`     TINYINT UNSIGNED NOT NULL,
    `reserved_at`  INT UNSIGNED    NULL,
    `available_at` INT UNSIGNED    NOT NULL,
    `created_at`   INT UNSIGNED    NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
    `id`             VARCHAR(255) NOT NULL,
    `name`           VARCHAR(255) NOT NULL,
    `total_jobs`     INT          NOT NULL,
    `pending_jobs`   INT          NOT NULL,
    `failed_jobs`    INT          NOT NULL,
    `failed_job_ids` LONGTEXT     NOT NULL,
    `options`        MEDIUMTEXT   NULL,
    `cancelled_at`   INT          NULL,
    `created_at`     INT          NOT NULL,
    `finished_at`    INT          NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`       VARCHAR(255)    NOT NULL,
    `connection` TEXT            NOT NULL,
    `queue`      TEXT            NOT NULL,
    `payload`    LONGTEXT        NOT NULL,
    `exception`  LONGTEXT        NOT NULL,
    `failed_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: migrations (control de versiones Laravel)
CREATE TABLE IF NOT EXISTS `migrations` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch`     INT          NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLAS DE AUTENTICACIÓN (Laravel Sanctum)
-- ============================================================

-- Tabla: personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255)    NOT NULL,
    `tokenable_id`   BIGINT UNSIGNED NOT NULL,
    `name`           TEXT            NOT NULL,
    `token`          VARCHAR(64)     NOT NULL,
    `abilities`      TEXT            NULL,
    `last_used_at`   TIMESTAMP       NULL,
    `expires_at`     TIMESTAMP       NULL,
    `created_at`     TIMESTAMP       NULL,
    `updated_at`     TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    INDEX `personal_access_tokens_tokenable_index` (`tokenable_type`, `tokenable_id`),
    INDEX `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO DE CONTROL DE ACCESO
-- ============================================================

-- Tabla: roles
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(30)     NOT NULL,
    `description` VARCHAR(200)    NULL,
    `status`      TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP       NULL,
    `updated_at`  TIMESTAMP       NULL,
    `deleted_at`  TIMESTAMP       NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: modules
CREATE TABLE IF NOT EXISTS `modules` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(255)    NOT NULL,
    `slug`            VARCHAR(255)    NOT NULL,
    `icon`            VARCHAR(255)    NULL,
    `route`           VARCHAR(255)    NULL,
    `parent_id`       BIGINT UNSIGNED NULL,
    `is_active`       TINYINT(1)      NOT NULL DEFAULT 1,
    `sort_order`      INT             NOT NULL DEFAULT 0,
    `show_in_sidebar` TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`      TIMESTAMP       NULL,
    `updated_at`      TIMESTAMP       NULL,
    `deleted_at`      TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `modules_slug_unique` (`slug`),
    CONSTRAINT `modules_parent_id_foreign`
        FOREIGN KEY (`parent_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: permissions
CREATE TABLE IF NOT EXISTS `permissions` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL,
    `slug`       VARCHAR(255)    NULL,
    `created_at` TIMESTAMP       NULL,
    `updated_at` TIMESTAMP       NULL,
    `deleted_at` TIMESTAMP       NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: role_module_permissions
CREATE TABLE IF NOT EXISTS `role_module_permissions` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_id`       BIGINT UNSIGNED NOT NULL,
    `module_id`     BIGINT UNSIGNED NOT NULL,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_role_module_permission` (`role_id`, `module_id`, `permission_id`),
    CONSTRAINT `rmp_role_id_foreign`
        FOREIGN KEY (`role_id`)       REFERENCES `roles`       (`id`) ON DELETE CASCADE,
    CONSTRAINT `rmp_module_id_foreign`
        FOREIGN KEY (`module_id`)     REFERENCES `modules`     (`id`) ON DELETE CASCADE,
    CONSTRAINT `rmp_permission_id_foreign`
        FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO DE USUARIOS
-- ============================================================

-- Tabla: users
CREATE TABLE IF NOT EXISTS `users` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(50)     NOT NULL,
    `last_name`      VARCHAR(60)     NOT NULL,
    `password`       VARCHAR(255)    NOT NULL,
    `email`          VARCHAR(255)    NULL,
    `phone`          VARCHAR(20)     NULL,
    `status`         TINYINT(1)      NOT NULL DEFAULT 1,
    `role_id`        BIGINT UNSIGNED NULL,
    `remember_token` VARCHAR(100)    NULL,
    `created_at`     TIMESTAMP       NULL,
    `updated_at`     TIMESTAMP       NULL,
    `deleted_at`     TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `users_role_id_foreign`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email`      VARCHAR(255) NOT NULL,
    `token`      VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP    NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: sessions
CREATE TABLE IF NOT EXISTS `sessions` (
    `id`            VARCHAR(255)    NOT NULL,
    `user_id`       BIGINT UNSIGNED NULL,
    `ip_address`    VARCHAR(45)     NULL,
    `user_agent`    TEXT            NULL,
    `payload`       LONGTEXT        NOT NULL,
    `last_activity` INT             NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `sessions_user_id_index`       (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: user_details
CREATE TABLE IF NOT EXISTS `user_details` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `gender`       VARCHAR(14)     NULL,
    `birthdate`    DATE            NULL,
    `address`      VARCHAR(100)    NULL,
    `addon_address`VARCHAR(50)     NULL,
    `notes`        TEXT            NULL,
    `user_id`      BIGINT UNSIGNED NOT NULL,
    `created_at`   TIMESTAMP       NULL,
    `updated_at`   TIMESTAMP       NULL,
    `deleted_at`   TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `user_details_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO DE CATÁLOGO DE EQUIPOS
-- ============================================================

-- Tabla: categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)    NOT NULL,
    `slug`        VARCHAR(120)    NOT NULL,
    `description` TEXT            NULL,
    `status`      TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP       NULL,
    `updated_at`  TIMESTAMP       NULL,
    `deleted_at`  TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_name_unique` (`name`),
    UNIQUE KEY `categories_slug_unique` (`slug`),
    INDEX `categories_status_name_index` (`status`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: equipment
CREATE TABLE IF NOT EXISTS `equipment` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` BIGINT UNSIGNED NULL,
    `name`        VARCHAR(150)    NOT NULL,
    `code`        VARCHAR(50)     NOT NULL,
    `description` TEXT            NULL,
    `stock`       INT UNSIGNED    NOT NULL DEFAULT 1,
    `status`      ENUM('available', 'maintenance', 'out_of_service') NOT NULL DEFAULT 'available',
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP       NULL,
    `updated_at`  TIMESTAMP       NULL,
    `deleted_at`  TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `equipment_code_unique` (`code`),
    INDEX `equipment_category_id_status_index` (`category_id`, `status`),
    INDEX `equipment_is_active_name_index`     (`is_active`, `name`),
    CONSTRAINT `equipment_category_id_foreign`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: equipment_images
CREATE TABLE IF NOT EXISTS `equipment_images` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `image_path`   VARCHAR(300)    NULL,
    `image_name`   VARCHAR(300)    NULL,
    `is_primary`   TINYINT(1)      NOT NULL DEFAULT 0,
    `equipment_id` BIGINT UNSIGNED NOT NULL,
    `created_at`   TIMESTAMP       NULL,
    `updated_at`   TIMESTAMP       NULL,
    `deleted_at`   TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    INDEX `equipment_images_equipment_id_is_primary_index` (`equipment_id`, `is_primary`),
    CONSTRAINT `equipment_images_equipment_id_foreign`
        FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO DE RESERVAS
-- ============================================================

-- Tabla: reservations
CREATE TABLE IF NOT EXISTS `reservations` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`          BIGINT UNSIGNED NOT NULL,
    `equipment_id`     BIGINT UNSIGNED NOT NULL,
    `start_time`       DATETIME        NOT NULL,
    `end_time`         DATETIME        NOT NULL,
    `status`           ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `notes`            TEXT            NULL,
    `rejection_reason` TEXT            NULL,
    `approved_by`      BIGINT UNSIGNED NULL,
    `approved_at`      TIMESTAMP       NULL,
    `created_at`       TIMESTAMP       NULL,
    `updated_at`       TIMESTAMP       NULL,
    `deleted_at`       TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    INDEX `reservations_equipment_start_end_index` (`equipment_id`, `start_time`, `end_time`),
    INDEX `reservations_user_id_status_index`      (`user_id`, `status`),
    INDEX `reservations_approved_by_status_index`  (`approved_by`, `status`),
    CONSTRAINT `reservations_user_id_foreign`
        FOREIGN KEY (`user_id`)      REFERENCES `users`     (`id`) ON DELETE CASCADE,
    CONSTRAINT `reservations_equipment_id_foreign`
        FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reservations_approved_by_foreign`
        FOREIGN KEY (`approved_by`)  REFERENCES `users`     (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: reservation_logs
CREATE TABLE IF NOT EXISTS `reservation_logs` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `reservation_id` BIGINT UNSIGNED NOT NULL,
    `user_id`        BIGINT UNSIGNED NULL,
    `action`         VARCHAR(100)    NOT NULL,
    `description`    TEXT            NULL,
    `created_at`     TIMESTAMP       NULL,
    `updated_at`     TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    INDEX `reservation_logs_reservation_id_action_index` (`reservation_id`, `action`),
    CONSTRAINT `reservation_logs_reservation_id_foreign`
        FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reservation_logs_user_id_foreign`
        FOREIGN KEY (`user_id`)        REFERENCES `users`        (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- MÓDULO DE REPORTES
-- ============================================================

-- Tabla: report_requests
CREATE TABLE IF NOT EXISTS `report_requests` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `type`       ENUM('reservations', 'equipment_usage', 'user_activity') NOT NULL,
    `start_date` DATE            NULL,
    `end_date`   DATE            NULL,
    `status`     ENUM('pending', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    `filters`    JSON            NULL,
    `created_at` TIMESTAMP       NULL,
    `updated_at` TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    INDEX `report_requests_user_id_status_index` (`user_id`, `status`),
    INDEX `report_requests_type_status_index`    (`type`, `status`),
    CONSTRAINT `report_requests_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: reports
CREATE TABLE IF NOT EXISTS `reports` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `report_request_id` BIGINT UNSIGNED NOT NULL,
    `file_path`         VARCHAR(255)    NOT NULL,
    `file_name`         VARCHAR(150)    NULL,
    `file_type`         VARCHAR(20)     NOT NULL DEFAULT 'pdf',
    `generated_at`      TIMESTAMP       NULL,
    `created_at`        TIMESTAMP       NULL,
    `updated_at`        TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    INDEX `reports_report_request_id_index` (`report_request_id`),
    INDEX `reports_generated_at_index`      (`generated_at`),
    CONSTRAINT `reports_report_request_id_foreign`
        FOREIGN KEY (`report_request_id`) REFERENCES `report_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DATOS INICIALES (Seeders)
-- ============================================================

-- ------------------------------------------------------------
-- Roles
-- ------------------------------------------------------------
INSERT INTO `roles` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin',               'Acceso total al sistema',                                   1, NOW(), NOW()),
(2, 'Administrador',             'Administra usuarios, equipos, reservas y reportes',          1, NOW(), NOW()),
(3, 'Encargado de Laboratorio',  'Gestiona equipos y reservas',                               1, NOW(), NOW()),
(4, 'Docente',                   'Solicita reservas y consulta reportes',                     1, NOW(), NOW()),
(5, 'Estudiante',                'Solicita y consulta sus reservas',                          1, NOW(), NOW());

-- ------------------------------------------------------------
-- Módulos
-- IDs 1-5: módulos raíz (parent_id NULL)
-- IDs 6-13: submódulos (referencia a módulos raíz)
-- ------------------------------------------------------------
INSERT INTO `modules` (`id`, `name`, `slug`, `icon`, `route`, `parent_id`, `is_active`, `sort_order`, `show_in_sidebar`, `created_at`, `updated_at`) VALUES
-- Módulos raíz
(1,  'Dashboard',               'dashboard',              'mdi-view-dashboard',  '/dashboard',        NULL, 1, 1, 1, NOW(), NOW()),
(2,  'Gestión de Catálogo',     'catalog-management',     'mdi-laptop',          NULL,                NULL, 1, 2, 1, NOW(), NOW()),
(3,  'Gestión de Reservas',     'reservation-management', 'mdi-calendar-clock',  NULL,                NULL, 1, 3, 1, NOW(), NOW()),
(4,  'Gestión de Reportes',     'report-management',      'mdi-file-chart',      NULL,                NULL, 1, 4, 1, NOW(), NOW()),
(5,  'Gestión de Acceso',       'access-management',      'mdi-shield-account',  NULL,                NULL, 1, 5, 1, NOW(), NOW()),
-- Submódulos de Catálogo (parent_id=2)
(6,  'Categorías',              'categories',             'mdi-shape',           '/categories',       2,    1, 1, 1, NOW(), NOW()),
(7,  'Equipos',                 'equipment',              'mdi-desktop-classic', '/equipment',        2,    1, 2, 1, NOW(), NOW()),
-- Submódulos de Reservas (parent_id=3)
(8,  'Reservas',                'reservations',           'mdi-calendar',        '/reservations',     3,    1, 1, 1, NOW(), NOW()),
(9,  'Historial de Reservas',   'reservation-logs',       'mdi-history',         '/reservation-logs', 3,    1, 2, 1, NOW(), NOW()),
-- Submódulos de Reportes (parent_id=4)
(10, 'Solicitudes de Reportes', 'report-requests',        'mdi-file-send',       '/report-requests',  4,    1, 1, 1, NOW(), NOW()),
(11, 'Reportes',                'reports',                'mdi-file-chart',      '/reports',          4,    1, 2, 1, NOW(), NOW()),
-- Submódulos de Acceso (parent_id=5)
(12, 'Usuarios',                'users',                  'mdi-account-group',   '/users',            5,    1, 1, 1, NOW(), NOW()),
(13, 'Roles',                   'roles',                  'mdi-badge-account',   '/roles',            5,    1, 2, 1, NOW(), NOW());

-- ------------------------------------------------------------
-- Permisos
-- ------------------------------------------------------------
INSERT INTO `permissions` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Ver',      'view',   NOW(), NOW()),
(2, 'Crear',    'create', NOW(), NOW()),
(3, 'Editar',   'edit',   NOW(), NOW()),
(4, 'Eliminar', 'delete', NOW(), NOW());

-- ------------------------------------------------------------
-- Permisos por Rol
-- Formato: (role_id, module_id, permission_id)
-- ------------------------------------------------------------
INSERT INTO `role_module_permissions` (`role_id`, `module_id`, `permission_id`, `created_at`, `updated_at`) VALUES
-- ► Super Admin (1): todos los módulos (1-13) × todos los permisos (1-4)
(1,1,1,NOW(),NOW()),(1,1,2,NOW(),NOW()),(1,1,3,NOW(),NOW()),(1,1,4,NOW(),NOW()),
(1,2,1,NOW(),NOW()),(1,2,2,NOW(),NOW()),(1,2,3,NOW(),NOW()),(1,2,4,NOW(),NOW()),
(1,3,1,NOW(),NOW()),(1,3,2,NOW(),NOW()),(1,3,3,NOW(),NOW()),(1,3,4,NOW(),NOW()),
(1,4,1,NOW(),NOW()),(1,4,2,NOW(),NOW()),(1,4,3,NOW(),NOW()),(1,4,4,NOW(),NOW()),
(1,5,1,NOW(),NOW()),(1,5,2,NOW(),NOW()),(1,5,3,NOW(),NOW()),(1,5,4,NOW(),NOW()),
(1,6,1,NOW(),NOW()),(1,6,2,NOW(),NOW()),(1,6,3,NOW(),NOW()),(1,6,4,NOW(),NOW()),
(1,7,1,NOW(),NOW()),(1,7,2,NOW(),NOW()),(1,7,3,NOW(),NOW()),(1,7,4,NOW(),NOW()),
(1,8,1,NOW(),NOW()),(1,8,2,NOW(),NOW()),(1,8,3,NOW(),NOW()),(1,8,4,NOW(),NOW()),
(1,9,1,NOW(),NOW()),(1,9,2,NOW(),NOW()),(1,9,3,NOW(),NOW()),(1,9,4,NOW(),NOW()),
(1,10,1,NOW(),NOW()),(1,10,2,NOW(),NOW()),(1,10,3,NOW(),NOW()),(1,10,4,NOW(),NOW()),
(1,11,1,NOW(),NOW()),(1,11,2,NOW(),NOW()),(1,11,3,NOW(),NOW()),(1,11,4,NOW(),NOW()),
(1,12,1,NOW(),NOW()),(1,12,2,NOW(),NOW()),(1,12,3,NOW(),NOW()),(1,12,4,NOW(),NOW()),
(1,13,1,NOW(),NOW()),(1,13,2,NOW(),NOW()),(1,13,3,NOW(),NOW()),(1,13,4,NOW(),NOW()),

-- ► Administrador (2): módulos 1,6,7,8,9,10,11,12,13 × todos los permisos
(2,1,1,NOW(),NOW()),(2,1,2,NOW(),NOW()),(2,1,3,NOW(),NOW()),(2,1,4,NOW(),NOW()),
(2,6,1,NOW(),NOW()),(2,6,2,NOW(),NOW()),(2,6,3,NOW(),NOW()),(2,6,4,NOW(),NOW()),
(2,7,1,NOW(),NOW()),(2,7,2,NOW(),NOW()),(2,7,3,NOW(),NOW()),(2,7,4,NOW(),NOW()),
(2,8,1,NOW(),NOW()),(2,8,2,NOW(),NOW()),(2,8,3,NOW(),NOW()),(2,8,4,NOW(),NOW()),
(2,9,1,NOW(),NOW()),(2,9,2,NOW(),NOW()),(2,9,3,NOW(),NOW()),(2,9,4,NOW(),NOW()),
(2,10,1,NOW(),NOW()),(2,10,2,NOW(),NOW()),(2,10,3,NOW(),NOW()),(2,10,4,NOW(),NOW()),
(2,11,1,NOW(),NOW()),(2,11,2,NOW(),NOW()),(2,11,3,NOW(),NOW()),(2,11,4,NOW(),NOW()),
(2,12,1,NOW(),NOW()),(2,12,2,NOW(),NOW()),(2,12,3,NOW(),NOW()),(2,12,4,NOW(),NOW()),
(2,13,1,NOW(),NOW()),(2,13,2,NOW(),NOW()),(2,13,3,NOW(),NOW()),(2,13,4,NOW(),NOW()),

-- ► Encargado de Laboratorio (3): módulos 1,6,7,8,9,10,11 × todos los permisos
(3,1,1,NOW(),NOW()),(3,1,2,NOW(),NOW()),(3,1,3,NOW(),NOW()),(3,1,4,NOW(),NOW()),
(3,6,1,NOW(),NOW()),(3,6,2,NOW(),NOW()),(3,6,3,NOW(),NOW()),(3,6,4,NOW(),NOW()),
(3,7,1,NOW(),NOW()),(3,7,2,NOW(),NOW()),(3,7,3,NOW(),NOW()),(3,7,4,NOW(),NOW()),
(3,8,1,NOW(),NOW()),(3,8,2,NOW(),NOW()),(3,8,3,NOW(),NOW()),(3,8,4,NOW(),NOW()),
(3,9,1,NOW(),NOW()),(3,9,2,NOW(),NOW()),(3,9,3,NOW(),NOW()),(3,9,4,NOW(),NOW()),
(3,10,1,NOW(),NOW()),(3,10,2,NOW(),NOW()),(3,10,3,NOW(),NOW()),(3,10,4,NOW(),NOW()),
(3,11,1,NOW(),NOW()),(3,11,2,NOW(),NOW()),(3,11,3,NOW(),NOW()),(3,11,4,NOW(),NOW()),

-- ► Docente (4): módulos 1,8,10,11 × solo [Ver(1), Crear(2)]
(4,1,1,NOW(),NOW()),(4,1,2,NOW(),NOW()),
(4,8,1,NOW(),NOW()),(4,8,2,NOW(),NOW()),
(4,10,1,NOW(),NOW()),(4,10,2,NOW(),NOW()),
(4,11,1,NOW(),NOW()),(4,11,2,NOW(),NOW()),

-- ► Estudiante (5): módulos 1,8 × solo [Ver(1), Crear(2)]
(5,1,1,NOW(),NOW()),(5,1,2,NOW(),NOW()),
(5,8,1,NOW(),NOW()),(5,8,2,NOW(),NOW());

-- ------------------------------------------------------------
-- Categorías
-- ------------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Computadores',             'computadores',             'Equipos de cómputo para prácticas de laboratorio',          1, NOW(), NOW()),
(2, 'Proyectores',              'proyectores',              'Equipos de proyección para clases y exposiciones',           1, NOW(), NOW()),
(3, 'Kits Electrónicos',        'kits-electronicos',        'Componentes y kits para prácticas de electrónica',           1, NOW(), NOW()),
(4, 'Instrumentos de Medición', 'instrumentos-de-medicion', 'Multímetros, osciloscopios y herramientas de medición',      1, NOW(), NOW());

-- ------------------------------------------------------------
-- Equipos
-- ------------------------------------------------------------
INSERT INTO `equipment` (`id`, `category_id`, `name`, `code`, `description`, `stock`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Portátil Dell Latitude 5420', 'EQ-001', 'Portátil para prácticas de programación',   10, 'available',   1, NOW(), NOW()),
(2, 1, 'Portátil HP ProBook 440',     'EQ-002', 'Equipo portátil para uso académico',          8, 'available',   1, NOW(), NOW()),
(3, 2, 'Video Beam Epson X49',        'EQ-003', 'Proyector multimedia para presentaciones',    4, 'available',   1, NOW(), NOW()),
(4, 3, 'Kit Arduino Uno',             'EQ-004', 'Kit básico de prototipado con Arduino',      15, 'available',   1, NOW(), NOW()),
(5, 4, 'Multímetro Digital Uni-T',    'EQ-005', 'Instrumento para medición eléctrica',         6, 'maintenance', 1, NOW(), NOW());

-- ------------------------------------------------------------
-- Usuarios
-- ADVERTENCIA: El hash aquí es un PLACEHOLDER (corresponde a
-- la contraseña 'password', NO a 'Password123!').
-- Para obtener hashes reales, ejecute después:
--   php artisan db:seed --class=UserSeeder
-- O genere el hash con:
--   php -r "echo password_hash('Password123!', PASSWORD_BCRYPT, ['cost'=>12]);"
-- ------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `last_name`, `email`, `password`, `phone`, `status`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'Super',  'Admin',   'superadmin@labnova.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '300000001', 1, 1, NOW(), NOW()),
(2, 'Ana',    'Torres',  'admin@labnova.com',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '300000002', 1, 2, NOW(), NOW()),
(3, 'Carlos', 'Ramirez', 'laboratorio@labnova.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '300000003', 1, 3, NOW(), NOW()),
(4, 'Laura',  'Martinez','docente@labnova.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '300000004', 1, 4, NOW(), NOW()),
(5, 'Juan',   'Perez',   'estudiante@labnova.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '300000005', 1, 5, NOW(), NOW());

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
