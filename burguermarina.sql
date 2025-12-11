-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 11-12-2025 a las 17:42:14
-- Versión del servidor: 11.8.3-MariaDB-ubu2404
-- Versión de PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `burguermarina`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias`
--

CREATE TABLE `Categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `Categorias`
--

INSERT INTO `Categorias` (`id_categoria`, `nombre`) VALUES
(1, 'Hamburguesas'),
(2, 'Bebidas'),
(3, 'Postres');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Comentarios`
--

CREATE TABLE `Comentarios` (
  `id_comentario` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_plato` int(11) NOT NULL,
  `contenido` text DEFAULT NULL,
  `valoracion` int(11) DEFAULT NULL CHECK (`valoracion` >= 1 and `valoracion` <= 5),
  `fecha` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Detalle_Pedido`
--

CREATE TABLE `Detalle_Pedido` (
  `id_pedido` int(11) NOT NULL,
  `id_plato` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Pedidos`
--

CREATE TABLE `Pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `metodo_entrega` varchar(50) DEFAULT NULL,
  `direccion_empresa` varchar(255) DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Platos`
--

CREATE TABLE `Platos` (
  `id_plato` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `Platos`
--

INSERT INTO `Platos` (`id_plato`, `nombre`, `descripcion`, `precio`, `imagen`, `id_categoria`) VALUES
(1, 'Marina BBQ', 'Carne vacuno, aros de cebolla, bacon y salsa BBQ casera', 14.50, 'bbq.jpg', 1),
(2, 'Trufada Gourmet', 'Carne vacuno, crema de trufa negra y parmesano', 15.90, 'trufa.jpg', 1),
(3, 'Chicken Crispy', 'Pollo crujiente estilo sureño con mayonesa', 11.50, 'pollo.jpg', 1),
(4, 'Veggie Marina', 'Hamburguesa de Heura con aguacate y tomate', 13.00, 'veggie.jpg', 1),
(5, 'Patatas Bravas', 'Nuestras patatas con salsa brava secreta', 5.50, 'bravas.jpg', 3),
(6, 'Tequeños', 'Palitos de queso venezolano con mermelada', 7.90, 'teque.jpg', 3),
(7, 'Nuggets de Pollo', '6 unidades con salsa a elegir', 6.00, 'nuggets.jpg', 3),
(8, 'Agua Mineral', 'Botella 50cl', 1.50, 'agua.jpg', 2),
(9, 'Cerveza Turia', 'Tostada de barril', 2.80, 'turia.jpg', 2),
(10, 'Fanta Naranja', 'Lata 33cl', 2.50, 'fanta.jpg', 2),
(11, 'Cheesecake', 'Tarta de queso casera con arándanos', 5.50, 'cheese.jpg', 1),
(12, 'Brownie', 'Con nueces y bola de helado de vainilla', 6.00, 'brownie.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios`
--

CREATE TABLE `Usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Volcado de datos para la tabla `Usuarios`
--

INSERT INTO `Usuarios` (`id_usuario`, `nombre`, `email`, `password`, `telefono`, `rol`) VALUES
(12, 'Super Admin', 'admin@test.com', '$2y$10$xyv.eNvjvQdkZE4fUYs3OOrDliTnh26Z43Nlo89Y6oLenF86lrgtW', NULL, 'admin'),
(13, 'Usuario Normal', 'user@test.com', '$2y$10$xyv.eNvjvQdkZE4fUYs3OOrDliTnh26Z43Nlo89Y6oLenF86lrgtW', NULL, 'user');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Categorias`
--
ALTER TABLE `Categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `Comentarios`
--
ALTER TABLE `Comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_plato` (`id_plato`);

--
-- Indices de la tabla `Detalle_Pedido`
--
ALTER TABLE `Detalle_Pedido`
  ADD PRIMARY KEY (`id_pedido`,`id_plato`),
  ADD KEY `id_plato` (`id_plato`);

--
-- Indices de la tabla `Pedidos`
--
ALTER TABLE `Pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `Platos`
--
ALTER TABLE `Platos`
  ADD PRIMARY KEY (`id_plato`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Categorias`
--
ALTER TABLE `Categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Comentarios`
--
ALTER TABLE `Comentarios`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Pedidos`
--
ALTER TABLE `Pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Platos`
--
ALTER TABLE `Platos`
  MODIFY `id_plato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Comentarios`
--
ALTER TABLE `Comentarios`
  ADD CONSTRAINT `Comentarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`),
  ADD CONSTRAINT `Comentarios_ibfk_2` FOREIGN KEY (`id_plato`) REFERENCES `Platos` (`id_plato`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Detalle_Pedido`
--
ALTER TABLE `Detalle_Pedido`
  ADD CONSTRAINT `Detalle_Pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `Pedidos` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `Detalle_Pedido_ibfk_2` FOREIGN KEY (`id_plato`) REFERENCES `Platos` (`id_plato`);

--
-- Filtros para la tabla `Pedidos`
--
ALTER TABLE `Pedidos`
  ADD CONSTRAINT `Pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id_usuario`);

--
-- Filtros para la tabla `Platos`
--
ALTER TABLE `Platos`
  ADD CONSTRAINT `Platos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `Categorias` (`id_categoria`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
