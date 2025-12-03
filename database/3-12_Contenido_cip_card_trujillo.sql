-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-12-2025 a las 00:00:10
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
-- Base de datos: `cip_card_trujillo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-admin@example.com|127.0.0.1', 'i:1;', 1764688000),
('laravel-cache-admin@example.com|127.0.0.1:timer', 'i:1764688000;', 1764688000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunicados`
--

CREATE TABLE `comunicados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `url_enlace` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_eventos`
--

CREATE TABLE `detalle_eventos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_publicaciones`
--

CREATE TABLE `detalle_publicaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `publicacion_id` bigint(20) UNSIGNED NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `url_enlace` varchar(255) DEFAULT NULL,
  `grupo` varchar(20) DEFAULT 'galeria',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_publicacion` date NOT NULL,
  `seccion` varchar(50) NOT NULL COMMENT 'institucion, junta, convocatorias',
  `categoria` varchar(50) DEFAULT NULL COMMENT 'normativa, tarifario, etc.',
  `ruta_archivo` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_evento` date NOT NULL,
  `lugar` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_23_235548_create_permission_tables', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `organizacion_card`
--

CREATE TABLE `organizacion_card` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL,
  `grupo` varchar(50) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE `publicaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `seccion` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-11-24 04:59:01', '2025-11-24 04:59:01'),
(2, 'gestor_contenido', 'web', '2025-11-24 04:59:01', '2025-11-24 04:59:01'),
(3, 'mesa_partes', 'web', '2025-11-24 04:59:01', '2025-11-24 04:59:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('xKzrqohit6u6ojwxyu7cK34Bn550J7fjv3y0Hdra', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia0MxckNjR3RERGtQeHdzdVhCaFJzTWVYTHUwVXQzNEdsdVRLV1lHMyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9sb2NhbGhvc3Q6OTAwMC9tZXNhLXBhcnRlcy9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTt9', 1763959117);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas_configuracion`
--

CREATE TABLE `tarifas_configuracion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clave` varchar(50) NOT NULL COMMENT 'Identificador único para usar en código',
  `valor` decimal(15,2) NOT NULL COMMENT 'Valor numérico',
  `activo` tinyint(1) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL COMMENT 'Explicación de qué es este valor',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarifas_configuracion`
--

INSERT INTO `tarifas_configuracion` (`id`, `clave`, `valor`, `activo`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'tasa_solicitud', 500.00, 1, 'Tasa por presentación de solicitud de arbitraje', '2025-12-03 20:54:45', '2025-12-03 22:41:15'),
(2, 'tasa_recusacion', 700.00, 1, 'Tasa por recusación de árbitro', '2025-12-03 20:54:45', '2025-12-03 22:41:19'),
(3, 'tasa_incorporacion', 600.00, 1, 'Incorporación a la nómina de árbitros', '2025-12-03 20:54:45', '2025-12-03 22:41:22'),
(4, 'porcentaje_indeterminado', 5.00, 1, 'Porcentaje aplicado a contrato original en cuantía indeterminada', '2025-12-03 20:54:45', '2025-12-03 22:41:24'),
(5, 'igv', 18.00, 1, 'Impuesto General a las Ventas (IGV) actual', '2025-12-03 20:54:45', '2025-12-03 22:41:27'),
(7, 'clave_prueba', 44.00, 0, NULL, '2025-12-04 03:42:43', '2025-12-04 03:43:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas_escalas`
--

CREATE TABLE `tarifas_escalas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('arbitro_unico','tribunal_arbitral','gastos_administrativos') NOT NULL COMMENT 'Define la tabla de origen del PDF',
  `rango_letra` varchar(5) NOT NULL COMMENT 'Ej: A, B, C...',
  `monto_min` decimal(15,2) NOT NULL COMMENT 'Inicio del rango',
  `monto_max` decimal(15,2) DEFAULT NULL COMMENT 'Fin del rango. NULL para rangos "A más" o infinitos',
  `monto_fijo` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'La tarifa base fija',
  `porcentaje_exceso` decimal(5,3) NOT NULL DEFAULT 0.000 COMMENT 'Ej: 0.70 para 0.70%. Guardar como valor porcentual',
  `base_exceso` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Monto sobre el cual se calcula el exceso',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarifas_escalas`
--

INSERT INTO `tarifas_escalas` (`id`, `tipo`, `rango_letra`, `monto_min`, `monto_max`, `monto_fijo`, `porcentaje_exceso`, `base_exceso`, `activo`, `created_at`, `updated_at`) VALUES
(2, 'arbitro_unico', 'B', 145001.00, 291000.00, 6650.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-04 02:05:31'),
(3, 'arbitro_unico', 'C', 290001.00, 870000.00, 14454.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(4, 'arbitro_unico', 'D', 870001.00, 1450000.00, 16658.50, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(5, 'arbitro_unico', 'E', 1450001.00, 2050000.00, 23218.50, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(6, 'arbitro_unico', 'F', 2050001.00, 2900000.00, 25428.50, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(7, 'arbitro_unico', 'G', 2900001.00, 14500000.00, 25428.50, 0.700, 2900001.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(8, 'arbitro_unico', 'H', 14500001.00, 80000000.00, 62548.50, 0.230, 14500001.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(9, 'arbitro_unico', 'I', 80000001.00, NULL, 0.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(10, 'tribunal_arbitral', 'A', 0.00, 145000.00, 15600.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(11, 'tribunal_arbitral', 'B', 145001.00, 290000.00, 18326.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(12, 'tribunal_arbitral', 'C', 290001.00, 870000.00, 25411.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(13, 'tribunal_arbitral', 'D', 870001.00, 1450000.00, 31896.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(14, 'tribunal_arbitral', 'E', 1450001.00, 2050000.00, 52821.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(15, 'tribunal_arbitral', 'F', 2050001.00, 2900000.00, 58346.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(16, 'tribunal_arbitral', 'G', 2900001.00, 14500000.00, 58346.00, 0.800, 2900001.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(17, 'tribunal_arbitral', 'H', 14500001.00, 80000000.00, 149942.00, 0.700, 14500001.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(18, 'tribunal_arbitral', 'I', 80000001.00, NULL, 0.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(19, 'gastos_administrativos', 'A', 0.00, 145000.00, 3200.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(20, 'gastos_administrativos', 'B', 145001.00, 290000.00, 7203.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(21, 'gastos_administrativos', 'C', 290001.00, 870000.00, 12413.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(22, 'gastos_administrativos', 'D', 870001.00, 1450000.00, 14437.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(23, 'gastos_administrativos', 'E', 1450001.00, 2050000.00, 17116.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(24, 'gastos_administrativos', 'F', 2050001.00, 2900000.00, 19616.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(25, 'gastos_administrativos', 'G', 2900001.00, 14500000.00, 19616.00, 0.350, 2900001.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(26, 'gastos_administrativos', 'H', 14500001.00, 80000000.00, 55100.00, 0.300, 14500001.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(27, 'gastos_administrativos', 'I', 80000001.00, NULL, 0.00, 0.000, 0.00, 1, '2025-12-03 20:55:01', '2025-12-03 20:55:01'),
(30, 'arbitro_unico', 'A', 0.00, 145000.00, 5200.00, 0.000, 0.00, 1, '2025-12-04 03:31:15', '2025-12-03 22:56:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Anthony Joel Palma Rojas', 't513300520@unitru.edu.pe', NULL, '$2y$12$ClW/62NKxnRv3oGw4bIhcO6NRJUX8jeUP1U0O/TR54ITlaxpkwoM.', NULL, '2025-11-24 08:31:14', '2025-11-24 08:31:14'),
(2, 'Admin', 'admin@gmail.com', NULL, '$2y$12$2fiuKduJOAUy43r9XHfZ7eLjXU60jht5V88nuhru18NPypp6YZdia', NULL, '2025-11-24 08:33:24', '2025-11-24 08:33:24'),
(3, 'Gestor', 'gestor@gmail.com', NULL, '$2y$12$KWaaULULlkoFiRtZFgpIn.Jf9rlT9Sj5mZHAzy3ezddRA.gRzVcmC', NULL, '2025-11-24 08:34:51', '2025-11-24 08:34:51'),
(4, 'Anthony Joel Palma Rojas', 't513300525@unitru.edu.pe', NULL, '$2y$12$v5pbTf5bdu5M8yodnHNVfO5aV39Jnx60bx770X.MTQiB7gyI1qh4m', NULL, '2025-11-24 09:37:11', '2025-11-24 09:37:11'),
(5, 'Anthony Joel Palma Rojas', 't5133005440@unitru.edu.pe', NULL, '$2y$12$Gyz7moalyRlrgTaKdhE2huwssCPrHzizuWslsJtnitkr6lcrU/Vke', NULL, '2025-11-24 09:38:36', '2025-11-24 09:38:36');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `comunicados`
--
ALTER TABLE `comunicados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_eventos`
--
ALTER TABLE `detalle_eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_evento_detalle` (`evento_id`);

--
-- Indices de la tabla `detalle_publicaciones`
--
ALTER TABLE `detalle_publicaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_publicacion_detalle` (`publicacion_id`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `organizacion_card`
--
ALTER TABLE `organizacion_card`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tarifas_configuracion`
--
ALTER TABLE `tarifas_configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `tarifas_escalas`
--
ALTER TABLE `tarifas_escalas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comunicados`
--
ALTER TABLE `comunicados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_eventos`
--
ALTER TABLE `detalle_eventos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_publicaciones`
--
ALTER TABLE `detalle_publicaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `organizacion_card`
--
ALTER TABLE `organizacion_card`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tarifas_configuracion`
--
ALTER TABLE `tarifas_configuracion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tarifas_escalas`
--
ALTER TABLE `tarifas_escalas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_eventos`
--
ALTER TABLE `detalle_eventos`
  ADD CONSTRAINT `fk_evento_detalle` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_publicaciones`
--
ALTER TABLE `detalle_publicaciones`
  ADD CONSTRAINT `fk_publicacion_detalle` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
