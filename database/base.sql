create database cip_card_trujillo;

/* 1. TABLA MAESTRA (La Publicación en sí) */
CREATE TABLE `publicaciones` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `seccion` varchar(50) NOT NULL, /* Ej: 'presentacion', 'slider_home' */
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 2. TABLA DETALLE (Las Imágenes asociadas) */
CREATE TABLE `detalle_publicaciones` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `publicacion_id` bigint(20) UNSIGNED NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `url_enlace` varchar(255) DEFAULT NULL,
  `grupo` varchar(20) DEFAULT 'galeria', /* 'principal' o 'galeria' */
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_publicacion_detalle` FOREIGN KEY (`publicacion_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comunicados` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `url_enlace` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eventos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_evento` date NOT NULL, /* Fecha específica del evento */
  `lugar` varchar(255) DEFAULT NULL, /* Opcional */
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 2. TABLA DETALLE: IMÁGENES DEL EVENTO */
CREATE TABLE `detalle_eventos` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `evento_id` bigint(20) UNSIGNED NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `tipo` varchar(20) NOT NULL, /* 'principal' o 'galeria' */
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_evento_detalle` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `organizacion_card` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  
  /* Datos Personales */
  `nombres` varchar(255) NOT NULL,
  `cargo` varchar(255) DEFAULT NULL, /* Ej: 'Decano', 'Vicedecano', 'Secretaria General' */
  `email` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  
  /* Foto Circular */
  `ruta_imagen` varchar(255) DEFAULT NULL,
  
  /* Clasificación para mostrar en la web */
  /* Valores sugeridos: 
     - 'directivo' (Órgano Directivo - CIP Lima)
     - 'decisorio_presidente' (Órgano Decisorio - Presidente)
     - 'decisorio_miembros' (Órgano Decisorio - Miembros)
     - 'secretaria' (Órgano de Gestión - Secretaría General)
     - 'secretarios_arbitrales'
     - 'apoyo' (Personal de Apoyo)
     - 'administrativo' (Soporte Administrativo)
  */
  `grupo` varchar(50) NOT NULL, 
  
  /* Orden visual (1, 2, 3...) para que el jefe salga primero */
  `orden` int(11) NOT NULL DEFAULT 0, 
  
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE documentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    fecha_publicacion DATE NOT NULL,
    seccion VARCHAR(50) NOT NULL COMMENT 'institucion, junta, convocatorias',
    categoria VARCHAR(50) NULL COMMENT 'normativa, tarifario, etc.',
    ruta_archivo VARCHAR(255) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tarifas_escalas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  
  `tipo` ENUM('arbitro_unico', 'tribunal_arbitral', 'gastos_administrativos') NOT NULL COMMENT 'Define la tabla de origen del PDF',
  
  `rango_letra` VARCHAR(5) NOT NULL COMMENT 'Ej: A, B, C...',
  
  `monto_min` DECIMAL(15, 2) NOT NULL COMMENT 'Inicio del rango',
  `monto_max` DECIMAL(15, 2) NULL COMMENT 'Fin del rango. NULL para rangos "A más" o infinitos',
  
  `monto_fijo` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'La tarifa base fija',
  `porcentaje_exceso` DECIMAL(5, 3) NOT NULL DEFAULT 0.000 COMMENT 'Ej: 0.70 para 0.70%. Guardar como valor porcentual',
  `base_exceso` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Monto sobre el cual se calcula el exceso',
  
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tarifas_configuracion` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clave` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Identificador único para usar en código',
  `valor` DECIMAL(15, 2) NOT NULL COMMENT 'Valor numérico',
  `descripcion` VARCHAR(255) NULL COMMENT 'Explicación de qué es este valor',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
