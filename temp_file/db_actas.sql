-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-11-2023 a las 03:11:35
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.2.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_actas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_reversos_rvs`
--

CREATE TABLE `tbl_reversos_rvs` (
  `rvs_id` int(11) NOT NULL,
  `rvs_clave` varchar(5) NOT NULL,
  `rvs_ruta` text NOT NULL,
  `rvs_status` char(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tbl_reversos_rvs`
--

INSERT INTO `tbl_reversos_rvs` (`rvs_id`, `rvs_clave`, `rvs_ruta`, `rvs_status`) VALUES
(1, 'GR', '', '1');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_reversos_rvs`
--
ALTER TABLE `tbl_reversos_rvs`
  ADD PRIMARY KEY (`rvs_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_reversos_rvs`
--
ALTER TABLE `tbl_reversos_rvs`
  MODIFY `rvs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
