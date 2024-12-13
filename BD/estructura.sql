-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 13-12-2024 a las 17:08:44
-- Versión del servidor: 8.1.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_restaurante`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

DROP TABLE IF EXISTS `mesas`;
CREATE TABLE IF NOT EXISTS `mesas` (
  `id_mesa` int NOT NULL AUTO_INCREMENT,
  `capacidad` int NOT NULL,
  `id_sala` int DEFAULT NULL,
  PRIMARY KEY (`id_mesa`),
  KEY `id_sala_idx` (`id_sala`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id_mesa`, `capacidad`, `id_sala`) VALUES
(1, 6, 1),
(2, 6, 1),
(3, 6, 1),
(4, 6, 1),
(5, 6, 2),
(6, 6, 2),
(7, 6, 2),
(10, 6, 2),
(11, 6, 2),
(15, 6, 3),
(17, 6, 3),
(18, 6, 3),
(20, 6, 4),
(21, 6, 4),
(26, 6, 4),
(27, 6, 4),
(28, 6, 4),
(29, 6, 5),
(30, 6, 5),
(31, 6, 5),
(32, 6, 5),
(34, 6, 6),
(35, 6, 6),
(36, 6, 6),
(37, 6, 6),
(38, 6, 6),
(39, 6, 7),
(40, 6, 7),
(41, 6, 7),
(42, 6, 7),
(43, 6, 8),
(44, 6, 8),
(45, 6, 8),
(46, 6, 8),
(47, 6, 8),
(57, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

DROP TABLE IF EXISTS `reservas`;
CREATE TABLE IF NOT EXISTS `reservas` (
  `id_reserva` int NOT NULL AUTO_INCREMENT,
  `id_mesa` int NOT NULL,
  `nombre_cliente` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hora_reserva` datetime NOT NULL,
  `hora_fin` datetime NOT NULL,
  `fecha_reserva` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `camarero_id` int NOT NULL,
  PRIMARY KEY (`id_reserva`),
  KEY `fk_mesa` (`id_mesa`),
  KEY `fk_camarero` (`camarero_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_mesa`, `nombre_cliente`, `hora_reserva`, `hora_fin`, `fecha_reserva`, `camarero_id`) VALUES
(7, 34, 'uuuuu', '2024-12-09 19:36:00', '2024-12-09 21:00:00', '2024-12-09 18:36:44', 3),
(8, 35, 'fvgg', '2024-12-09 19:39:00', '2024-12-09 20:00:00', '2024-12-09 18:39:50', 3),
(9, 37, 'fesg', '2024-12-09 19:41:00', '2024-12-09 20:00:00', '2024-12-09 18:42:00', 3),
(10, 2, 'pepe', '2024-12-09 19:57:00', '2024-12-09 20:00:00', '2024-12-09 18:57:28', 3),
(11, 3, 'errrrrr', '2024-12-09 20:20:00', '2024-12-09 21:00:00', '2024-12-09 19:03:56', 3),
(12, 4, 'MJFVNJEFN', '2024-12-10 22:22:00', '2024-12-10 23:33:00', '2024-12-09 19:04:42', 3),
(16, 1, 'Eduardo', '2024-12-10 20:00:00', '2024-12-10 21:00:00', '2024-12-10 10:17:39', 2),
(20, 1, 'deiby', '2024-12-11 12:30:00', '2024-12-11 14:00:00', '2024-12-11 11:12:30', 1),
(21, 5, 'pepe', '2024-12-11 23:00:00', '2024-12-11 23:30:00', '2024-12-11 18:29:22', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salas`
--

DROP TABLE IF EXISTS `salas`;
CREATE TABLE IF NOT EXISTS `salas` (
  `id_sala` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `capacidad` int NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_sala`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `salas`
--

INSERT INTO `salas` (`id_sala`, `nombre`, `capacidad`, `imagen`) VALUES
(1, 'Terraza 1', 24, '../uploads/PrivadaBtn.png'),
(2, 'Terraza 2', 30, '../uploads/PrivadaBtn.png'),
(3, 'Terraza 3', 30, '../uploads/PrivadaBtn.png'),
(4, 'Comedor 1', 30, '../uploads/ComedorBtn.jpg'),
(5, 'Comedor 2', 30, '../uploads/ComedorBtn.jpg'),
(6, 'Sala Privada 1', 30, '../uploads/Comedores.png'),
(7, 'Sala Privada 2', 30, '../uploads/Comedores.png'),
(8, 'Sala Privada 3', 30, '../uploads/Comedores.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sillas`
--

DROP TABLE IF EXISTS `sillas`;
CREATE TABLE IF NOT EXISTS `sillas` (
  `id_silla` int NOT NULL AUTO_INCREMENT,
  `id_mesa` int NOT NULL,
  PRIMARY KEY (`id_silla`),
  KEY `fk_mesa_sillas` (`id_mesa`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `sillas`
--

INSERT INTO `sillas` (`id_silla`, `id_mesa`) VALUES
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(57, 3),
(58, 3),
(59, 3),
(60, 3),
(61, 3),
(62, 3),
(51, 4),
(52, 4),
(53, 4),
(54, 4),
(55, 4),
(56, 4),
(45, 5),
(46, 5),
(47, 5),
(48, 5),
(49, 5),
(50, 5),
(67, 6),
(68, 6),
(69, 6),
(70, 6),
(71, 6),
(72, 6),
(73, 7),
(74, 7),
(75, 7),
(76, 7),
(77, 7),
(78, 7),
(79, 10),
(80, 10),
(81, 10),
(82, 10),
(83, 10),
(84, 10),
(85, 11),
(86, 11),
(87, 11),
(88, 11),
(89, 11),
(90, 11),
(97, 15),
(98, 15),
(99, 15),
(100, 15),
(101, 15),
(102, 15),
(103, 17),
(104, 17),
(105, 17),
(106, 17),
(107, 17),
(108, 17),
(109, 18),
(110, 18),
(111, 18),
(112, 18),
(113, 18),
(114, 18),
(115, 20),
(116, 20),
(117, 20),
(118, 20),
(119, 20),
(120, 20),
(121, 21),
(122, 21),
(123, 21),
(124, 21),
(125, 21),
(126, 21),
(127, 26),
(128, 26),
(129, 26),
(130, 26),
(131, 26),
(132, 26),
(139, 27),
(140, 27),
(141, 27),
(142, 27),
(143, 27),
(144, 27),
(145, 28),
(146, 28),
(147, 28),
(148, 28),
(149, 28),
(150, 28),
(133, 29),
(134, 29),
(135, 29),
(136, 29),
(137, 29),
(138, 29),
(151, 30),
(152, 30),
(153, 30),
(154, 30),
(155, 30),
(156, 30),
(157, 31),
(158, 31),
(159, 31),
(160, 31),
(161, 31),
(162, 31),
(163, 32),
(164, 32),
(165, 32),
(166, 32),
(167, 32),
(168, 32),
(169, 34),
(170, 34),
(171, 34),
(172, 34),
(173, 34),
(174, 34),
(175, 35),
(176, 35),
(177, 35),
(178, 35),
(179, 35),
(180, 35),
(181, 36),
(182, 36),
(183, 36),
(184, 36),
(185, 36),
(186, 36),
(187, 37),
(188, 37),
(189, 37),
(190, 37),
(191, 37),
(192, 37),
(199, 38),
(200, 38),
(201, 38),
(202, 38),
(203, 38),
(204, 38),
(193, 39),
(194, 39),
(195, 39),
(196, 39),
(197, 39),
(198, 39),
(229, 40),
(230, 40),
(231, 40),
(232, 40),
(233, 40),
(234, 40),
(235, 41),
(236, 41),
(237, 41),
(238, 41),
(239, 41),
(240, 41),
(241, 42),
(242, 42),
(243, 42),
(244, 42),
(245, 42),
(246, 42),
(33, 43),
(34, 43),
(35, 43),
(36, 43),
(37, 43),
(38, 43),
(205, 44),
(206, 44),
(207, 44),
(208, 44),
(209, 44),
(210, 44),
(211, 45),
(212, 45),
(213, 45),
(214, 45),
(215, 45),
(216, 45),
(223, 46),
(224, 46),
(225, 46),
(226, 46),
(227, 46),
(228, 46),
(217, 47),
(218, 47),
(219, 47),
(220, 47),
(221, 47),
(222, 47),
(63, 57),
(64, 57),
(65, 57),
(66, 57);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `contraseña` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo_usuario` enum('camarero','manager','mantenimiento','administrador') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `contraseña`, `tipo_usuario`) VALUES
(1, 'Aina Orozco', '$2a$12$zxGWHLfK1Ss0qIh9xD960ONgZ98PO.YAMAO2zYEfQYIF/fl0AWTVG', 'manager'),
(2, 'David Alvarez', '$2a$12$zxGWHLfK1Ss0qIh9xD960ONgZ98PO.YAMAO2zYEfQYIF/fl0AWTVG', 'camarero'),
(3, 'Deiby Buenano', '$2a$12$zxGWHLfK1Ss0qIh9xD960ONgZ98PO.YAMAO2zYEfQYIF/fl0AWTVG', 'camarero'),
(4, 'Pol Marc Monter', '$2a$12$zxGWHLfK1Ss0qIh9xD960ONgZ98PO.YAMAO2zYEfQYIF/fl0AWTVG', 'manager'),
(5, 'Angel Campos', '$2y$10$X/awi/5gcn4GeCzXq3RHBus0uB4290qKrONViZt07fH4Movhi56Du', 'mantenimiento'),
(6, 'Hugo Alda', '$2y$10$mjt/z6m1vmWWe6K6MEWkIOghlsYfLWzY65qDGd88EynHpqdvTo3Fi', 'administrador');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD CONSTRAINT `id_sala` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id_sala`);

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_camarero` FOREIGN KEY (`camarero_id`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `mesas` (`id_mesa`);

--
-- Filtros para la tabla `sillas`
--
ALTER TABLE `sillas`
  ADD CONSTRAINT `fk_mesa_sillas` FOREIGN KEY (`id_mesa`) REFERENCES `mesas` (`id_mesa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
