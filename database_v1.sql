-- phpMyAdmin SQL Dump
-- version 4.4.7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 11-11-2015 a las 00:21:29
-- Versión del servidor: 5.6.25
-- Versión de PHP: 5.5.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `oauth`
--
CREATE DATABASE IF NOT EXISTS `oauth` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `oauth`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_key` varchar(80) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT '1970-01-01 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `api_key`, `creation_date`) VALUES
(1, 'John Smith', 'john.smith@gmail.com', '$2a$10$8b41beda74dd4ac310dfeeX0BugwIfunk5swQqjSWqJSsvB3pNsLa', 'KYpaqw27RIej98RfnlQ0k6t48FM2Pr5Fvo0HDXLNK9L4yMgOEAaGQIicwLw76VPEwwfcJtHl9SeqAIFS', '2015-10-31 14:23:48'),
(2, 'Alice Anderson', 'alice.anderson@yahoo.com', '$2a$10$068818cbcec1a057c2b7cuHsNBSqeiDnUkWTk33ayBX1AHaE2Yma6', 'MG2tmLhH0lj3P37QV6hVJSUF1m52oM5tAJuayC5LjuKSLA9oTO6OlDbamno3T8kgRkGtfFQyJnc0yiQA', '2015-10-23 00:00:00'),
(3, 'Bob Hawkins', 'bob.hawkins@hotmail.com', '$2a$10$663eb21c70a49782349f9u6Lg/lZOmvdxf/lFn8uSeUwxelRugBWa', 'hbzMIPM6w3jTg5DVf/NBW04dvT6AwQtmLhH0lj3P37QV6hVJSUFakNYoJKSAc', '2015-10-31 21:52:49');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
