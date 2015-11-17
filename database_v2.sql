-- phpMyAdmin SQL Dump
-- version 4.4.7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-11-2015 a las 06:46:17
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
-- Estructura de tabla para la tabla `apps`
--

DROP TABLE IF EXISTS `apps`;
CREATE TABLE IF NOT EXISTS `apps` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `apps`
--

INSERT INTO `apps` (`client_id`, `client_secret`, `name`, `user_id`) VALUES
('nkU350F4PfKLf9umf798qX6jlP2ya501', 'v2Q0m12CB5tM36xJ4K4g2Vv06ASD52HD', 'Custom 3rd party App', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens`
--

DROP TABLE IF EXISTS `tokens`;
CREATE TABLE IF NOT EXISTS `tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `expiration` datetime NOT NULL,
  `client_id` varchar(80) NOT NULL COMMENT 'Client_ID of the App',
  `value` varchar(80) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tokens`
--

INSERT INTO `tokens` (`id`, `user_id`, `type`, `expiration`, `client_id`, `value`) VALUES
(4, 1, 'code', '2015-11-11 10:22:35', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_KM9jQe00RyUUEsynv83ymicZ8QXRbUVociHDuCBodKWYUoC0mbcVxA=='),
(5, 1, 'access', '2015-11-11 09:22:37', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_+wVM0sGgFAmAq80F5Or0SaVOKLc2JUHxs5I5wbZe8DpesyxIQ4Ry2g=='),
(6, 1, 'refresh', '2016-11-11 09:22:37', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_CrrPPi1lzCQDCxrl6T+DZXumJ6yRhOOLyL7pZapbkzPrQgQOr5V1aw=='),
(7, 1, 'code', '2015-11-16 11:40:23', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_Kv6J8I8PSkGbSFhYgW5wV7969TMbRelUV7FnOD3BK6YByTvS8gObPQ=='),
(8, 1, 'access', '2015-11-16 11:40:25', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_6vtA5S+Dm7PLxfPQf2AHe+laL20mq5fWwobpFyrm21AUOhwhMDZZSg=='),
(9, 1, 'access', '2015-11-16 13:00:26', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_9tbl4Kwi4ZITRJLDGBdv8Byh8Nhj/UejtwsObOZhJ4YekBUlTPRrtA=='),
(10, 1, 'access', '2015-11-16 15:23:04', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_rueBmPvCOxiFIOw+nXzAQyFd3uTNfStk9JdVQLfYVGpcjw7bBU3onw=='),
(11, 1, 'code', '2015-11-16 14:29:53', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_XCDONvs36OujYeuyt1ul2WOAyjI1ORUGi+C9SZuXOy/dKJmiq3mTYQ=='),
(12, 1, 'access', '2015-11-16 14:29:54', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_XNjRbKcI2L9eeq9OayidYYR/tU5Q4qvNNYW2xVHKnM+n+7oYzNpYJw=='),
(13, 1, 'code', '2015-11-16 14:34:53', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_CFt34jpl45APe/HtIi6p29Q0AB0y7J2kN3MOSnm9i8Ix6OaUHQmJuw=='),
(14, 1, 'access', '2015-11-16 14:34:57', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_qOgfKr841q44O/ure4moNk80Umj5kptGp+khP3k5Al43lDpH5hf6iA=='),
(15, 1, 'code', '2015-11-16 14:37:28', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_s5ZJFRR0o1TrvFH6yuwupAvXF2rvDis8z3NtAlXiKwiagPCH0tl3vA=='),
(16, 1, 'code', '2015-11-16 14:37:50', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_xCiFSffcXMx6ekstXL4spPuIeMws/yOFR/KzpJYZQTs8vNtmRpZZNg=='),
(17, 1, 'access', '2015-11-16 14:37:52', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_Ad+rldVE7tr/96+5Xdd4HSKxewiFZLH/yilltPb1oLuoM1jKXBJ4Zw=='),
(18, 1, 'code', '2015-11-16 15:09:28', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_7XQ1+gHIAGVW/FVyNoov7PudJAoc6/LdPccIUNfiIBRnsULHleFXBg=='),
(19, 1, 'access', '2015-11-16 15:09:32', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_WjLJ6TMZ5DpeEUM1Fei4wIbjgXO6BWcYjXSSNgMXz1/0a7kjHTWW9Q=='),
(20, 1, 'code', '2015-11-16 15:09:35', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_KFx3sI1W303RJh+jWX6fGvi7n2C4AShLKEt6YJ3tP4D06ZJMIyWZbA=='),
(21, 1, 'access', '2015-11-16 15:09:36', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_UgLnvs9xzD2QtRoaUcKCgkhrl884QhY2cH4tDbiIS/lQXHNrLMU4eg=='),
(22, 1, 'code', '2015-11-16 15:10:36', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_s0nXmysPzeBHJj7dNOkgdv71WL8KJVrKkgmACCVnAZg1ZT6WO3tRRA=='),
(23, 1, 'access', '2015-11-16 15:11:13', 'nkU350F4PfKLf9umf798qX6jlP2ya501', '1_P6TkzhkpqiltW8zG6Vjdq1nYAHX1vBxg0pF9TrcmXB68Md/Tyo7GpQ==');

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
-- Indices de la tabla `apps`
--
ALTER TABLE `apps`
  ADD PRIMARY KEY (`client_id`);

--
-- Indices de la tabla `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de la tabla `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
