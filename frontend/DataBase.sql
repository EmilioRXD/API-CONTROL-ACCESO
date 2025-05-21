-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-05-2025 a las 13:07:24
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
(3, '2025-II - Primera Cuota', '2025-04-15'),
(4, '2025-II - Segunda Cuota', '2025-05-15'),
(5, '2025-II - Tercera Cuota', '2025-06-15');

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
(600345678, 'Carlos Eduardo', 'Gonzáles Rivas', 1),
(602876541, 'Valentina Sofía', 'Vargas Paredes', 2);

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
(33, 600345678, 3, 'PAGADO'),
(34, 600345678, 4, 'PAGADO'),
(35, 600345678, 5, 'PAGADO'),
(36, 602876541, 3, 'PAGADO'),
(37, 602876541, 4, 'VENCIDO'),
(38, 602876541, 5, 'PENDIENTE');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
