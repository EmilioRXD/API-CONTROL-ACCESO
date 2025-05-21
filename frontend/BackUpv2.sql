-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-05-2025 a las 06:50:01
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
-- Base de datos: `control_acceso`
--
CREATE DATABASE IF NOT EXISTS `control_acceso` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `control_acceso`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Carreras`
--

CREATE TABLE `Carreras` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Carreras`
--

INSERT INTO `Carreras` (`id`, `nombre`) VALUES
(1, 'TSU INFORMÁTICA'),
(2, 'TSU ADMINISTRACIÓN DE EMPRESAS'),
(3, 'TSU ADMINISTRACIÓN TRIBUTARIA'),
(4, 'TSU HIGIENE Y SEGURIDAD INDUSTRIAL'),
(6, 'TSU TECNOLOGÍA DE GAS'),
(7, 'TSU TECNOLOGÍA PETROLERA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Configuracion`
--

CREATE TABLE `Configuracion` (
  `id` int(11) NOT NULL,
  `parametro` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Configuracion`
--

INSERT INTO `Configuracion` (`id`, `parametro`, `valor`, `descripcion`) VALUES
(1, 'PERIODO_GRACIA_DIAS', '10', 'Período de gracia en días para cuotas vencidas'),
(2, 'BLOQUEO_ACCESO_VENCIDOS', 'true', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Controladores`
--

CREATE TABLE `Controladores` (
  `id` int(11) NOT NULL,
  `mac` char(17) NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `funcion` enum('LECTOR','ESCRITOR') NOT NULL DEFAULT 'LECTOR',
  `tipo_acceso` enum('ENTRADA','SALIDA','NO APLICA') NOT NULL DEFAULT 'ENTRADA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Controladores`
--

INSERT INTO `Controladores` (`id`, `mac`, `ubicacion`, `funcion`, `tipo_acceso`) VALUES
(1, 'B4:E6:2D:C5:8C:E9', 'Control de Estudio', 'ESCRITOR', 'NO APLICA'),
(22, 'FC:E8:C0:7E:44:48', 'Sede Principal', 'LECTOR', 'ENTRADA'),
(23, 'AA:BB:CC:DD:EE:FF', 'Sede Principal', 'LECTOR', 'SALIDA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cuotas`
--

CREATE TABLE `Cuotas` (
  `id` int(11) NOT NULL,
  `nombre_cuota` varchar(255) NOT NULL,
  `fecha_pago` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Cuotas`
--

INSERT INTO `Cuotas` (`id`, `nombre_cuota`, `fecha_pago`) VALUES
(3, '2025-II - Primera Cuota', '2025-05-19'),
(4, '2025-II - Segunda Cuota', '2025-05-13'),
(5, '2025-II - Tercera Cuota', '2025-06-11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Estudiantes`
--

CREATE TABLE `Estudiantes` (
  `cedula` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `id_carrera` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Estudiantes`
--

INSERT INTO `Estudiantes` (`cedula`, `nombre`, `apellido`, `id_carrera`) VALUES
(22849588, 'Luis', 'Ramírez', 3),
(25284996, 'Diego', 'González', 4),
(30034068, 'Jesus', 'Rodriguez', 1),
(30111222, 'Ana', 'Sanchez', 2),
(30111333, 'Luis', 'Martinez', 3),
(30111444, 'Maria', 'Fernandez', 4),
(30111555, 'Carlos', 'Gomez', 6),
(30111666, 'Laura', 'Lopez', 7),
(30111777, 'Jose', 'Diaz', 1),
(30111888, 'Patricia', 'Rojas', 2),
(30251609, 'Giorgio', 'Moreno', 1),
(30998394, 'Jesser', 'Palma', 1),
(34590718, 'Ricardo', 'Vargas', 1),
(43031137, 'Carlos', 'Pérez', 7),
(48147631, 'Javier', 'Ortiz', 7),
(55913502, 'Ricardo', 'Pérez', 6),
(64350732, 'Miguel', 'Reyes', 4),
(66339173, 'Javier', 'Romero', 7),
(87008632, 'Diego', 'Cruz', 2),
(98022960, 'Luis', 'Reyes', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Pagos`
--

CREATE TABLE `Pagos` (
  `id` int(11) NOT NULL,
  `estudiante_cedula` int(11) NOT NULL,
  `cuota_id` int(11) NOT NULL,
  `estado` enum('PAGADO','PENDIENTE','VENCIDO') NOT NULL DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Pagos`
--

INSERT INTO `Pagos` (`id`, `estudiante_cedula`, `cuota_id`, `estado`) VALUES
(1, 30034068, 3, 'PAGADO'),
(2, 30034068, 4, 'PAGADO'),
(3, 30034068, 5, 'PAGADO'),
(4, 30251609, 3, 'PAGADO'),
(5, 30251609, 4, 'PENDIENTE'),
(6, 30251609, 5, 'PENDIENTE'),
(7, 30998394, 3, 'PAGADO'),
(8, 30998394, 4, 'VENCIDO'),
(9, 30998394, 5, 'PENDIENTE'),
(10, 30111222, 3, 'PAGADO'),
(11, 30111222, 4, 'PAGADO'),
(12, 30111222, 5, 'PENDIENTE'),
(13, 30111333, 3, 'PAGADO'),
(14, 30111333, 4, 'VENCIDO'),
(15, 30111333, 5, 'PENDIENTE'),
(16, 30111444, 3, 'PAGADO'),
(17, 30111444, 4, 'PAGADO'),
(18, 30111444, 5, 'PAGADO'),
(19, 30111555, 3, 'VENCIDO'),
(20, 30111555, 4, 'PENDIENTE'),
(21, 30111555, 5, 'PENDIENTE'),
(22, 30111666, 3, 'PAGADO'),
(23, 30111666, 4, 'PAGADO'),
(24, 30111666, 5, 'VENCIDO'),
(25, 30111777, 3, 'PAGADO'),
(26, 30111777, 4, 'PENDIENTE'),
(27, 30111777, 5, 'PENDIENTE'),
(28, 30111888, 3, 'PAGADO'),
(29, 30111888, 4, 'PAGADO'),
(30, 30111888, 5, 'PENDIENTE'),
(31, 22849588, 3, 'PAGADO'),
(32, 22849588, 4, 'VENCIDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Registros`
--

CREATE TABLE `Registros` (
  `id` int(11) NOT NULL,
  `id_tarjeta` int(11) NOT NULL,
  `id_controlador` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `acceso_permitido` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Registros`
--

INSERT INTO `Registros` (`id`, `id_tarjeta`, `id_controlador`, `fecha_hora`, `acceso_permitido`) VALUES
(2021, 10, 22, '2025-05-18 19:25:09', 1),
(2022, 10, 22, '2025-05-18 19:25:25', 1),
(2023, 10, 22, '2025-05-18 19:27:50', 1),
(2024, 10, 22, '2025-05-18 19:27:52', 1),
(2025, 10, 22, '2025-05-18 19:28:02', 1),
(2026, 10, 22, '2025-05-18 19:29:05', 1),
(2027, 10, 22, '2025-05-18 19:29:27', 1),
(2028, 10, 22, '2025-05-18 19:35:07', 1),
(2029, 10, 22, '2025-05-18 19:35:44', 1),
(2030, 10, 22, '2025-05-18 19:39:06', 1),
(2031, 10, 22, '2025-05-18 19:39:08', 1),
(2032, 10, 22, '2025-05-19 22:44:59', 1),
(2033, 10, 22, '2025-05-19 22:45:01', 1),
(2034, 10, 22, '2025-05-19 22:47:03', 1),
(2035, 10, 22, '2025-05-19 22:49:43', 0),
(2036, 10, 22, '2025-05-19 22:50:01', 1),
(2037, 10, 22, '2025-05-19 22:50:09', 1),
(2038, 10, 22, '2025-05-19 22:50:16', 1),
(2039, 10, 22, '2025-05-19 22:50:22', 1),
(2040, 10, 22, '2025-05-19 22:50:27', 0),
(2041, 10, 22, '2025-05-19 22:50:38', 0),
(2042, 10, 22, '2025-05-19 22:51:02', 0),
(2043, 10, 22, '2025-05-19 22:51:07', 1),
(2044, 10, 22, '2025-05-19 22:51:17', 0),
(2045, 10, 22, '2025-05-19 22:51:52', 0),
(2046, 10, 22, '2025-05-19 22:52:03', 1),
(2047, 10, 22, '2025-05-19 23:17:27', 0),
(2048, 10, 22, '2025-05-19 23:17:29', 0),
(2049, 10, 22, '2025-05-19 23:17:31', 0),
(2050, 10, 22, '2025-05-19 23:17:37', 0),
(2051, 10, 22, '2025-05-19 23:17:38', 0),
(2052, 10, 22, '2025-05-19 23:17:39', 0),
(2053, 10, 22, '2025-05-19 23:19:06', 0),
(2057, 10, 22, '2025-05-19 23:33:21', 0),
(2058, 10, 22, '2025-05-19 23:33:30', 0),
(2059, 10, 22, '2025-05-19 23:33:46', 0),
(2060, 10, 22, '2025-05-19 23:48:51', 0),
(2061, 10, 22, '2025-05-19 23:48:51', 0),
(2062, 10, 22, '2025-05-19 23:48:55', 0),
(2063, 10, 22, '2025-05-19 23:59:39', 0),
(2064, 10, 22, '2025-05-20 00:06:34', 0),
(2069, 10, 22, '2025-05-20 00:16:18', 0),
(2070, 10, 22, '2025-05-20 00:16:19', 0),
(2082, 10, 22, '2025-05-20 17:29:45', 0),
(2083, 10, 22, '2025-05-20 18:21:14', 0),
(2084, 10, 22, '2025-05-20 19:36:19', 0),
(2085, 10, 22, '2025-05-20 19:36:21', 0),
(2086, 10, 22, '2025-05-20 19:41:47', 0),
(2087, 10, 22, '2025-05-20 19:45:39', 0),
(2088, 10, 22, '2025-05-20 20:04:27', 0),
(2089, 10, 23, '2025-05-20 20:04:43', 1),
(2090, 10, 23, '2025-05-20 20:04:53', 0),
(2091, 10, 22, '2025-05-20 20:04:55', 1),
(2092, 10, 23, '2025-05-20 20:04:57', 1),
(2093, 10, 22, '2025-05-20 20:04:59', 1),
(2094, 10, 23, '2025-05-20 20:05:00', 1),
(2095, 10, 22, '2025-05-20 20:05:03', 1),
(2096, 10, 23, '2025-05-20 20:05:04', 1),
(2097, 10, 22, '2025-05-20 20:05:07', 1),
(2098, 10, 22, '2025-05-20 20:05:08', 0),
(2099, 10, 22, '2025-05-20 20:13:19', 0),
(2100, 10, 23, '2025-05-20 20:13:28', 1),
(2101, 10, 22, '2025-05-20 20:13:30', 1),
(2102, 10, 22, '2025-05-20 20:13:34', 0),
(2103, 10, 23, '2025-05-20 20:13:35', 1),
(2104, 10, 22, '2025-05-20 20:13:36', 1),
(2105, 10, 22, '2025-05-20 20:13:38', 0),
(2106, 10, 23, '2025-05-20 20:13:41', 1),
(2107, 10, 23, '2025-05-20 20:13:42', 0),
(2108, 10, 22, '2025-05-20 20:20:16', 1),
(2109, 10, 22, '2025-05-20 20:20:19', 0),
(2110, 10, 22, '2025-05-20 20:20:21', 0),
(2111, 10, 22, '2025-05-20 20:20:23', 0),
(2112, 10, 22, '2025-05-20 20:20:26', 0),
(2113, 10, 22, '2025-05-20 20:20:29', 0),
(2114, 10, 22, '2025-05-20 20:23:06', 0),
(2115, 10, 22, '2025-05-20 20:23:08', 0),
(2116, 10, 22, '2025-05-20 20:23:13', 0),
(2117, 10, 23, '2025-05-20 20:23:20', 1),
(2118, 10, 22, '2025-05-20 20:23:22', 1),
(2119, 10, 22, '2025-05-20 20:23:23', 0),
(2120, 10, 22, '2025-05-20 20:23:25', 0),
(2121, 10, 22, '2025-05-20 20:31:18', 0),
(2122, 10, 22, '2025-05-20 20:31:20', 0),
(2123, 10, 23, '2025-05-20 20:31:27', 1),
(2124, 10, 23, '2025-05-20 20:31:29', 0),
(2125, 10, 23, '2025-05-20 20:31:31', 0),
(2126, 10, 23, '2025-05-20 20:31:34', 0),
(2127, 10, 22, '2025-05-20 20:31:36', 1),
(2128, 10, 23, '2025-05-20 20:31:38', 1),
(2129, 10, 23, '2025-05-20 20:31:39', 0),
(2130, 10, 22, '2025-05-20 20:32:10', 1),
(2131, 10, 22, '2025-05-20 20:32:13', 0),
(2132, 10, 22, '2025-05-20 20:32:14', 0),
(2133, 10, 23, '2025-05-20 20:32:20', 1),
(2134, 10, 23, '2025-05-20 20:32:22', 0),
(2135, 10, 22, '2025-05-20 20:32:39', 1),
(2136, 10, 22, '2025-05-20 20:32:40', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Tarjetas`
--

CREATE TABLE `Tarjetas` (
  `id` int(11) NOT NULL,
  `serial` char(20) NOT NULL,
  `estudiante_cedula` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_expiracion` date NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Tarjetas`
--

INSERT INTO `Tarjetas` (`id`, `serial`, `estudiante_cedula`, `fecha_emision`, `fecha_expiracion`, `activa`) VALUES
(10, '671780611', 30998394, '2025-05-14', '2026-05-14', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios`
--

CREATE TABLE `Usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `correo_electronico` varchar(255) NOT NULL,
  `hash_contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Usuarios`
--

INSERT INTO `Usuarios` (`id`, `nombre`, `apellido`, `correo_electronico`, `hash_contraseña`) VALUES
(1, 'Emilio', 'Cabrera', 'admin@email.com', '$2b$12$Yo81IGd7kjKgrdogn0Q2Juv9xaXPzZQCKRrwN8M2bwvuRCtmu5MFO'),
(2, 'Miguelito', 'Cabrera', 'miguel@email.com', '$2b$12$/i.LcxmxgciVunquunD/9u372FJnaIhJqrf8BOIHuy4BtUfN0hKXi'),
(3, 'Jesser', 'Palma', 'jssrpalma3@gmail.com', '$2b$12$ZaaXmU0ZB1Gyi/O1LLPV8OevFEme3UOJgc26HO1s7AdCmShMvu1jS'),
(4, 'Luisa', 'Perez', 'luisa@email.com', '$2b$12$Q0bW5kKQ3eRkCzUMk2b8kuQlYoHnvEv.vH2W/V/VfKZAcEtRz9u4q'),
(5, 'Carlos', 'Mendoza', 'carlos@email.com', '$2b$12$R4FaKUym2YK6qT/fvL9UYeBFKNAnrZzkgeERcUPBwFX3IcxB7Uw8e');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Carreras`
--
ALTER TABLE `Carreras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Configuracion`
--
ALTER TABLE `Configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parametro` (`parametro`);

--
-- Indices de la tabla `Controladores`
--
ALTER TABLE `Controladores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Cuotas`
--
ALTER TABLE `Cuotas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Estudiantes`
--
ALTER TABLE `Estudiantes`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `id_carrera` (`id_carrera`);

--
-- Indices de la tabla `Pagos`
--
ALTER TABLE `Pagos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Pagos_index_0` (`estudiante_cedula`,`cuota_id`),
  ADD KEY `cuota_id` (`cuota_id`);

--
-- Indices de la tabla `Registros`
--
ALTER TABLE `Registros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tarjeta` (`id_tarjeta`),
  ADD KEY `id_controlador` (`id_controlador`);

--
-- Indices de la tabla `Tarjetas`
--
ALTER TABLE `Tarjetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_Tarjetas_estudiante_cedula` (`estudiante_cedula`);

--
-- Indices de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Carreras`
--
ALTER TABLE `Carreras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `Configuracion`
--
ALTER TABLE `Configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `Controladores`
--
ALTER TABLE `Controladores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `Cuotas`
--
ALTER TABLE `Cuotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `Pagos`
--
ALTER TABLE `Pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `Registros`
--
ALTER TABLE `Registros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2137;

--
-- AUTO_INCREMENT de la tabla `Tarjetas`
--
ALTER TABLE `Tarjetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Estudiantes`
--
ALTER TABLE `Estudiantes`
  ADD CONSTRAINT `Estudiantes_ibfk_1` FOREIGN KEY (`id_carrera`) REFERENCES `Carreras` (`id`);

--
-- Filtros para la tabla `Pagos`
--
ALTER TABLE `Pagos`
  ADD CONSTRAINT `Pagos_ibfk_1` FOREIGN KEY (`estudiante_cedula`) REFERENCES `Estudiantes` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Pagos_ibfk_2` FOREIGN KEY (`cuota_id`) REFERENCES `Cuotas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Registros`
--
ALTER TABLE `Registros`
  ADD CONSTRAINT `Registros_ibfk_1` FOREIGN KEY (`id_tarjeta`) REFERENCES `Tarjetas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Registros_ibfk_2` FOREIGN KEY (`id_controlador`) REFERENCES `Controladores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `Tarjetas`
--
ALTER TABLE `Tarjetas`
  ADD CONSTRAINT `Tarjetas_ibfk_1` FOREIGN KEY (`estudiante_cedula`) REFERENCES `Estudiantes` (`cedula`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
