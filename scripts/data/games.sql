-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 05-05-2026 a las 16:41:42
-- Versión del servidor: 11.8.6-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u952965051_game`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `folder_name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `age_group` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 0,
  `total_votes` int(11) DEFAULT 0,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `github_link` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `games`
--

INSERT INTO `games` (`id`, `title`, `description`, `author`, `folder_name`, `category`, `age_group`, `created_at`, `approved`, `total_votes`, `average_rating`, `github_link`) VALUES
(1, 'Adivina el Número', 'Juego simple para adivinar un número del 1 al 10', 'Admin', 'adivina-numero', 'Lógica', '6-8 años', '2025-06-24 00:13:52', 1, 1, 5.00, NULL),
(2, 'Memoria ASCII', 'Juego de memoria donde emparejas letras con sus códigos ASCII.', 'Agustin Muñoz', 'ascii-memory', 'Códigos de caracteres', '9-12 años', '2025-06-24 00:26:50', 1, 1, 4.00, NULL),
(3, 'Código Perdido', 'Juego narrativo que combina una historia de recuperación de memoria con desafíos para aprender Python.', 'Sofia Rodriguez', 'codigo-perdido', 'Python', '9-12 años', '2025-06-24 00:26:50', 1, 0, 0.00, NULL),
(4, 'Juego del Rosco', 'Juego de preguntas tipo rosco para poner a prueba tus conocimientos de programación.', 'Domingo Pérez', 'rosco', 'Programación', '9-12 años', '2025-06-24 00:26:50', 1, 0, 0.00, NULL),
(5, 'Snake Programer', 'En este juego, controlas una serpiente que, en lugar de comer simples manzanas o frutas, consume ´tokens de programación´ que van apareciendo en el tablero. Cada token representa un elemento de código (variables, funciones, operadores, etc.) que se va agregando a tu ´editor de código´ al ser consumido.', 'Sol Méndez', 'snake', 'Programación', '9-12 años', '2025-06-24 00:26:50', 1, 1, 3.00, NULL),
(6, 'PlayIA', 'Juego creado para el Taller de IA 2025', 'Informatica 2do - 2025', 'playia-1760618248', 'Ciencias', '13-16 años', '2025-10-16 12:37:28', 1, 0, 0.00, 'https://domingo1987.github.io/PlayIA');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folder_name` (`folder_name`),
  ADD UNIQUE KEY `github_link` (`github_link`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
