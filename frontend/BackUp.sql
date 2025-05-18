-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 18-05-2025 a las 10:32:20
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Carreras`
--

CREATE TABLE `Carreras` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cuotas`
--

CREATE TABLE `Cuotas` (
  `id` int(11) NOT NULL,
  `nombre_cuota` varchar(255) NOT NULL,
  `fecha_pago` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Configuracion`
--
ALTER TABLE `Configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Controladores`
--
ALTER TABLE `Controladores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cuotas`
--
ALTER TABLE `Cuotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Pagos`
--
ALTER TABLE `Pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Registros`
--
ALTER TABLE `Registros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Tarjetas`
--
ALTER TABLE `Tarjetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
