-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 05-05-2026 a las 16:41:54
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
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `google_picture` varchar(500) DEFAULT NULL,
  `role` enum('admin','creator','student') DEFAULT 'student',
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `google_id`, `google_picture`, `role`, `approved`, `created_at`, `last_login`) VALUES
(1, 'domingo', NULL, '$2y$10$wNlIUujHofzKJbtYVCOBBuLP1CFE.9itUUANrUB/.yMgqG8aLrXo6', NULL, NULL, 'admin', 1, '2026-04-28 19:17:29', NULL),
(14, 'creador', NULL, '$2y$10$JxcUgc/fv9C2rKHp//ckD.o7srdfIiOgY34gXATaGypvcaKtt/Bf.', NULL, NULL, 'creator', 1, '2026-04-28 19:17:29', NULL),
(17, 'usuario', NULL, '$2y$10$199/q27MMnxoXBKlI5usVOQFSVMzPuXw.Pe2HiOZk7DHX8QxrP6RK', NULL, NULL, 'student', 1, '2026-04-28 19:17:29', NULL),
(125, 'sebastian.alvarez', NULL, '$2y$10$CfiuZcayRElOTw8m/rKCK.iZ7o2aT8vC/.tj6.p03ZCq1kpyoBFL2', NULL, NULL, 'creator', 1, '2026-04-28 19:17:29', NULL),
(241, 'domingoperez1987', 'domingoperez1987@gmail.com', NULL, '107745109144381411135', 'https://lh3.googleusercontent.com/a/ACg8ocLv78ukzI_WIcF9W4zJ11BmCcmTpATctpCGZAbdMlHIk3Sm_X0XUQ=s96-c', 'student', 1, '2026-04-28 19:32:48', '2026-04-28 19:49:52'),
(244, 'pyonhatan', 'pyonhatan@gmail.com', NULL, '115891380333378711299', 'https://lh3.googleusercontent.com/a/ACg8ocIdpLdCIyT8J3OfEld5eAtPYO6YqHPNj2IwyS15-2aldOu0ctLo=s96-c', 'student', 1, '2026-04-28 19:33:24', '2026-05-01 15:14:43');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=403;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
