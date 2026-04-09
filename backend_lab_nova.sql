-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-04-2026 a las 02:10:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `backend_lab_nova`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Computadores', 'computadores', 'Equipos de cómputo para prácticas de laboratorio', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(2, 'Proyectores', 'proyectores', 'Equipos de proyección para clases y exposiciones', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(3, 'Kits Electrónicos', 'kits-electronicos', 'Componentes y kits para prácticas de electrónica', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(4, 'Instrumentos de Medición', 'instrumentos-de-medicion', 'Multímetros, osciloscopios y herramientas de medición', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipment`
--

CREATE TABLE `equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `status` enum('available','maintenance','out_of_service') NOT NULL DEFAULT 'available',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipment`
--

INSERT INTO `equipment` (`id`, `category_id`, `name`, `code`, `description`, `stock`, `status`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Portátil Dell Latitude 5420', 'EQ-001', 'Portátil para prácticas de programación', 10, 'available', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(2, 1, 'Portátil HP ProBook 440', 'EQ-002', 'Equipo portátil para uso académico', 8, 'available', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(3, 2, 'Video Beam Epson X49', 'EQ-003', 'Proyector multimedia para presentaciones', 4, 'available', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(4, 3, 'Kit Arduino Uno', 'EQ-004', 'Kit básico de prototipado con Arduino', 15, 'available', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(5, 4, 'Multímetro Digital Uni-T', 'EQ-005', 'Instrumento para medición eléctrica', 6, 'maintenance', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(6, 3, 'Microscopio Optico', 'MIC-001', 'Prueba', 8, 'available', 1, '2026-04-09 01:35:41', '2026-04-09 01:36:02', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipment_images`
--

CREATE TABLE `equipment_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(300) DEFAULT NULL,
  `image_name` varchar(300) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `show_in_sidebar` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modules`
--

INSERT INTO `modules` (`id`, `name`, `slug`, `icon`, `route`, `parent_id`, `is_active`, `sort_order`, `show_in_sidebar`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Dashboard', 'dashboard', 'mdi-view-dashboard', '/dashboard', NULL, 1, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(2, 'Gestión de Catálogo', 'catalog-management', 'mdi-laptop', NULL, NULL, 1, 2, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(3, 'Gestión de Reservas', 'reservation-management', 'mdi-calendar-clock', NULL, NULL, 1, 3, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(4, 'Gestión de Reportes', 'report-management', 'mdi-file-chart', NULL, NULL, 1, 4, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(5, 'Gestión de Acceso', 'access-management', 'mdi-shield-account', NULL, NULL, 1, 5, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(6, 'Categorías', 'categories', 'mdi-shape', '/categories', 2, 1, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(7, 'Equipos', 'equipment', 'mdi-desktop-classic', '/equipment', 2, 1, 2, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(8, 'Reservas', 'reservations', 'mdi-calendar', '/reservations', 3, 1, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(9, 'Historial de Reservas', 'reservation-logs', 'mdi-history', '/reservation-logs', 3, 1, 2, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(10, 'Solicitudes de Reportes', 'report-requests', 'mdi-file-send', '/report-requests', 4, 1, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(11, 'Reportes', 'reports', 'mdi-file-chart', '/reports', 4, 1, 2, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(12, 'Usuarios', 'users', 'mdi-account-group', '/users', 5, 1, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(13, 'Roles', 'roles', 'mdi-badge-account', '/roles', 5, 1, 2, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ver', 'view', '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(2, 'Crear', 'create', '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(3, 'Editar', 'edit', '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(4, 'Eliminar', 'delete', '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api-token', '830bf458a76d0493ab147f3e716755722f399f2e77330f9e5558236ab623102d', '[\"*\"]', '2026-04-01 05:58:57', NULL, '2026-04-01 02:29:43', '2026-04-01 05:58:57'),
(2, 'App\\Models\\User', 1, 'api-token', 'ee78baca95a635e1efba13089d493d43b5a3551e5df057aa953e842ee036186f', '[\"*\"]', NULL, NULL, '2026-04-01 02:29:54', '2026-04-01 02:29:54'),
(3, 'App\\Models\\User', 1, 'api-token', 'ffd5967953d26a430d40d3ce643c2754c8bbd4051fefeadfd4d18cb56009cf66', '[\"*\"]', '2026-04-01 02:51:41', NULL, '2026-04-01 02:47:09', '2026-04-01 02:51:41'),
(4, 'App\\Models\\User', 1, 'api-token', '1d7603984218d80b27c2613bfd548c2ff88ef507f43ce42413b874c8d5b8ad45', '[\"*\"]', '2026-04-01 06:05:17', NULL, '2026-04-01 02:48:58', '2026-04-01 06:05:17'),
(5, 'App\\Models\\User', 1, 'api-token', 'a000ace3f77151df233f4dc86ac0cb9da527078ded77c2c285a6dd20127bcf94', '[\"*\"]', '2026-04-01 06:28:22', NULL, '2026-04-01 02:52:16', '2026-04-01 06:28:22'),
(6, 'App\\Models\\User', 1, 'test', '1119fe8cc4f87326182bdedb9408f58d041301164ec24a8b365629a185ffdb3c', '[\"*\"]', '2026-04-01 05:59:51', NULL, '2026-04-01 05:52:57', '2026-04-01 05:59:51'),
(7, 'App\\Models\\User', 1, 'api-token', '396a1ceaa214d324a017b49585ca51bdc950ba081f45e1c1d956e61a27fe5701', '[\"*\"]', '2026-04-01 06:28:24', NULL, '2026-04-01 06:12:38', '2026-04-01 06:28:24'),
(8, 'App\\Models\\User', 1, 'api-token', '075f7a2fedf526ffd77c2061814a6b322243b31c8178abc4367ac08230a8f134', '[\"*\"]', '2026-04-09 04:59:47', NULL, '2026-04-09 01:30:40', '2026-04-09 04:59:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_request_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(150) DEFAULT NULL,
  `file_type` varchar(20) NOT NULL DEFAULT 'pdf',
  `generated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `report_requests`
--

CREATE TABLE `report_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('reservations','equipment_usage','user_activity') NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filters`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `status` enum('pending','approved','rejected','cancelled','completed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `equipment_id`, `start_time`, `end_time`, `status`, `notes`, `rejection_reason`, `approved_by`, `approved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 6, '2026-04-08 06:17:00', '2026-04-08 17:17:00', 'pending', 'esta es una prueba', NULL, NULL, NULL, '2026-04-09 03:17:17', '2026-04-09 03:17:17', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservation_logs`
--

CREATE TABLE `reservation_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reservation_logs`
--

INSERT INTO `reservation_logs` (`id`, `reservation_id`, `user_id`, `action`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'created', 'Reserva creada', '2026-04-09 03:17:17', '2026-04-09 03:17:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'Acceso total al sistema', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(2, 'Administrador', 'Administra usuarios, equipos, reservas y reportes', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(3, 'Encargado de Laboratorio', 'Gestiona equipos y reservas', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(4, 'Docente', 'Solicita reservas y consulta reportes', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL),
(5, 'Estudiante', 'Solicita y consulta sus reservas', 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_module_permissions`
--

CREATE TABLE `role_module_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_module_permissions`
--

INSERT INTO `role_module_permissions` (`id`, `role_id`, `module_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(2, 1, 1, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(3, 1, 1, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(4, 1, 1, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(5, 1, 2, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(6, 1, 2, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(7, 1, 2, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(8, 1, 2, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(9, 1, 3, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(10, 1, 3, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(11, 1, 3, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(12, 1, 3, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(13, 1, 4, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(14, 1, 4, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(15, 1, 4, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(16, 1, 4, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(17, 1, 5, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(18, 1, 5, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(19, 1, 5, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(20, 1, 5, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(21, 1, 6, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(22, 1, 6, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(23, 1, 6, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(24, 1, 6, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(25, 1, 7, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(26, 1, 7, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(27, 1, 7, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(28, 1, 7, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(29, 1, 8, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(30, 1, 8, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(31, 1, 8, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(32, 1, 8, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(33, 1, 9, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(34, 1, 9, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(35, 1, 9, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(36, 1, 9, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(37, 1, 10, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(38, 1, 10, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(39, 1, 10, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(40, 1, 10, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(41, 1, 11, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(42, 1, 11, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(43, 1, 11, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(44, 1, 11, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(45, 1, 12, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(46, 1, 12, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(47, 1, 12, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(48, 1, 12, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(49, 1, 13, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(50, 1, 13, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(51, 1, 13, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(52, 1, 13, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(53, 2, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(54, 2, 1, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(55, 2, 1, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(56, 2, 1, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(57, 2, 6, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(58, 2, 6, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(59, 2, 6, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(60, 2, 6, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(61, 2, 7, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(62, 2, 7, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(63, 2, 7, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(64, 2, 7, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(65, 2, 8, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(66, 2, 8, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(67, 2, 8, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(68, 2, 8, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(69, 2, 9, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(70, 2, 9, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(71, 2, 9, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(72, 2, 9, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(73, 2, 10, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(74, 2, 10, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(75, 2, 10, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(76, 2, 10, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(77, 2, 11, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(78, 2, 11, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(79, 2, 11, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(80, 2, 11, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(81, 2, 12, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(82, 2, 12, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(83, 2, 12, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(84, 2, 12, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(85, 2, 13, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(86, 2, 13, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(87, 2, 13, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(88, 2, 13, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(89, 3, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(90, 3, 1, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(91, 3, 1, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(92, 3, 1, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(93, 3, 6, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(94, 3, 6, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(95, 3, 6, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(96, 3, 6, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(97, 3, 7, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(98, 3, 7, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(99, 3, 7, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(100, 3, 7, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(101, 3, 8, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(102, 3, 8, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(103, 3, 8, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(104, 3, 8, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(105, 3, 9, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(106, 3, 9, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(107, 3, 9, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(108, 3, 9, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(109, 3, 10, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(110, 3, 10, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(111, 3, 10, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(112, 3, 10, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(113, 3, 11, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(114, 3, 11, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(115, 3, 11, 3, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(116, 3, 11, 4, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(117, 4, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(118, 4, 1, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(119, 4, 8, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(120, 4, 8, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(121, 4, 10, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(122, 4, 10, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(123, 4, 11, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(124, 4, 11, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(125, 5, 1, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(126, 5, 1, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(127, 5, 8, 1, '2026-03-31 21:18:33', '2026-03-31 21:18:33'),
(128, 5, 8, 2, '2026-03-31 21:18:33', '2026-03-31 21:18:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('mfweysiDT8iVEHHxgCSy8THlrmxCzmskV5yOnQse', NULL, '127.0.0.1', 'curl/8.12.1', 'eyJfdG9rZW4iOiI5VmdtY2pKcWNrM21MM0tvZ0FrdDY2QTIxS3QwRTBVZ3VkZDE5UU5lIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1774992258);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `last_name` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `last_name`, `password`, `email`, `phone`, `status`, `role_id`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super', 'Admin', '$2y$12$mRQVSGaZ9WPPTT/QLnzF9OPvStp1Ib5vsnXUtDB0uCHyVnW1omeXK', 'superadmin@labnova.com', '300000001', 1, 1, NULL, '2026-03-31 21:18:33', '2026-04-01 02:29:37', NULL),
(2, 'admin', '.', '$2y$12$MHJ3otKFekOMgv01F68zU./.ohQxxl2W2XMXcOPcpj1OxVTUP/MXW', 'admin@labnova.com', '300000002', 1, 2, NULL, '2026-03-31 21:18:33', '2026-04-09 03:16:25', NULL),
(3, 'Carlos', 'Ramirez', '$2y$12$Roc1pDsGtoF3wTsJ3GOjzuEzHA5GZ3O5sztcH5k/Op9euTZouXPny', 'laboratorio@labnova.com', '300000003', 1, 3, NULL, '2026-03-31 21:18:33', '2026-04-01 02:29:38', NULL),
(4, 'Laura', 'Martinez', '$2y$12$hHxgGJ986a8vFMWX076XXe5EpOA4YXIXHAmYeNz0PUdSLu30qIYEG', 'docente@labnova.com', '300000004', 1, 4, NULL, '2026-03-31 21:18:33', '2026-04-01 02:29:39', NULL),
(5, 'Juan', 'Perez', '$2y$12$94T0rS65n3Jhtxsgns0M1.RfYzi2StGdjd3gibzSXmzo9pRSXnV9q', 'estudiante@labnova.com', '300000005', 1, 5, NULL, '2026-03-31 21:18:33', '2026-04-01 06:00:16', NULL),
(23, 'Jose', 'Garcia', '$2y$12$7fb/zXXph6sYQBWzedGWU.PHnIknKebejOZ/YlrSUvhnygUThRjYO', 'gestarsoft@gmail.com', '3124522609', 1, 5, NULL, '2026-04-01 05:55:33', '2026-04-01 05:56:38', '2026-04-01 05:56:38'),
(24, 'Jose', 'Garcia', '$2y$12$rhOPtUJkx88l.R07jracVO10Pi4HqjadC3DdI9IH6iyLBJMBlE1B2', 'prueba@gmail.com', '30011385454', 1, 4, NULL, '2026-04-01 05:56:04', '2026-04-01 05:56:28', '2026-04-01 05:56:28'),
(25, 'prueba', 'prueba', '$2y$12$y/HpzWOQnFVJNOytoLeiV.u/LsVw5B8BAvhjShXerXr78Tqsg/56i', 'prueba@hotmail.com', '300123456', 1, 4, NULL, '2026-04-09 03:14:40', '2026-04-09 03:14:52', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_details`
--

CREATE TABLE `user_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gender` varchar(14) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `addon_address` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_details`
--

INSERT INTO `user_details` (`id`, `gender`, `birthdate`, `address`, `addon_address`, `notes`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, NULL, NULL, NULL, NULL, NULL, 23, '2026-04-01 05:55:33', '2026-04-01 05:55:33', NULL),
(3, NULL, NULL, NULL, NULL, NULL, 24, '2026-04-01 05:56:04', '2026-04-01 05:56:04', NULL),
(4, NULL, NULL, NULL, NULL, NULL, 5, '2026-04-01 05:58:13', '2026-04-01 05:58:13', NULL),
(5, NULL, NULL, NULL, NULL, NULL, 25, '2026-04-09 03:14:40', '2026-04-09 03:14:40', NULL),
(6, NULL, NULL, NULL, NULL, NULL, 2, '2026-04-09 03:15:15', '2026-04-09 03:15:15', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_status_name_index` (`status`,`name`);

--
-- Indices de la tabla `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_code_unique` (`code`),
  ADD KEY `equipment_category_id_status_index` (`category_id`,`status`),
  ADD KEY `equipment_is_active_name_index` (`is_active`,`name`);

--
-- Indices de la tabla `equipment_images`
--
ALTER TABLE `equipment_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_images_equipment_id_is_primary_index` (`equipment_id`,`is_primary`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modules_slug_unique` (`slug`),
  ADD KEY `modules_parent_id_foreign` (`parent_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_report_request_id_index` (`report_request_id`),
  ADD KEY `reports_generated_at_index` (`generated_at`);

--
-- Indices de la tabla `report_requests`
--
ALTER TABLE `report_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_requests_user_id_status_index` (`user_id`,`status`),
  ADD KEY `report_requests_type_status_index` (`type`,`status`);

--
-- Indices de la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_equipment_start_end_index` (`equipment_id`,`start_time`,`end_time`),
  ADD KEY `reservations_user_id_status_index` (`user_id`,`status`),
  ADD KEY `reservations_approved_by_status_index` (`approved_by`,`status`);

--
-- Indices de la tabla `reservation_logs`
--
ALTER TABLE `reservation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_logs_reservation_id_action_index` (`reservation_id`,`action`),
  ADD KEY `reservation_logs_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_module_permissions`
--
ALTER TABLE `role_module_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_module_permission` (`role_id`,`module_id`,`permission_id`),
  ADD KEY `rmp_module_id_foreign` (`module_id`),
  ADD KEY `rmp_permission_id_foreign` (`permission_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_details_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `equipment_images`
--
ALTER TABLE `equipment_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `report_requests`
--
ALTER TABLE `report_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reservation_logs`
--
ALTER TABLE `reservation_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `role_module_permissions`
--
ALTER TABLE `role_module_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `equipment_images`
--
ALTER TABLE `equipment_images`
  ADD CONSTRAINT `equipment_images_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_report_request_id_foreign` FOREIGN KEY (`report_request_id`) REFERENCES `report_requests` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `report_requests`
--
ALTER TABLE `report_requests`
  ADD CONSTRAINT `report_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservation_logs`
--
ALTER TABLE `reservation_logs`
  ADD CONSTRAINT `reservation_logs_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `role_module_permissions`
--
ALTER TABLE `role_module_permissions`
  ADD CONSTRAINT `rmp_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rmp_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rmp_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
