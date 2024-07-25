-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-07-2024 a las 09:02:48
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
-- Base de datos: `booksadre`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrador`
--

CREATE TABLE `administrador` (
  `id_administrador` int(10) UNSIGNED NOT NULL,
  `nombre_administrador` varchar(50) NOT NULL,
  `apellido_administrador` varchar(50) NOT NULL,
  `correo_administrador` varchar(100) NOT NULL,
  `alias_administrador` varchar(25) NOT NULL,
  `clave_administrador` varchar(100) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `administrador`
--

INSERT INTO `administrador` (`id_administrador`, `nombre_administrador`, `apellido_administrador`, `correo_administrador`, `alias_administrador`, `clave_administrador`, `fecha_registro`) VALUES
(1, 'fernando', 'moreno', 'fa3528028@gmail.com', 'maycol', '$2y$10$F7CULg.rndbxLY7TGyzR0.kfEFH3iAniz.Pm1s4TgGEOkbZnyfWN6', '2024-05-23 08:18:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(10) UNSIGNED NOT NULL,
  `nombre_categoria` varchar(50) NOT NULL,
  `descripcion_categoria` varchar(250) DEFAULT NULL,
  `imagen_categoria` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre_categoria`, `descripcion_categoria`, `imagen_categoria`) VALUES
(1, 'comics', 'historias con texto e imágenes dibujadas', '6661e8f07f3fa.jpg'),
(2, 'Novelas', 'obras literarias que relatan una historia', '6661e93d4a66b.jpg'),
(3, 'Libros de deporte', 'cuentan diferentes historias relacionadas con el deporte', '6661e9dd3c86b.jpg'),
(4, 'Novelas Románticas', 'historias maravillosas de amor, pasión y a veces también, desamor', '6661eb7ca7e4c.jpg'),
(5, 'Libros Autobiográficos', 'cuentan historias increíbles de personajes famosos y anónimos', '6661ebe369e33.jpg'),
(6, 'Aventuras', 'narran historias increíbles de grandes personajes que han ido a la conquista de lo desconocido', '66a088b541e82.jpeg'),
(7, 'libros de ciencia ficción', 'son obras de la literatura que construyen nuevos mundos, espacios, personajes y situaciones, ajenas y diferentes a nuestro mundo y nuestras vidas', '6661ec84edb0e.jpg'),
(8, 'Libros de Humor', 'permiten pasar buenos momentos, reír y disfrutar de la lectura', '6661ed6472a6a.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `nombre_cliente` varchar(50) NOT NULL,
  `apellido_cliente` varchar(50) NOT NULL,
  `dui_cliente` varchar(10) NOT NULL,
  `correo_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(9) NOT NULL,
  `direccion_cliente` varchar(250) NOT NULL,
  `nacimiento_cliente` date NOT NULL,
  `clave_cliente` varchar(100) NOT NULL,
  `estado_cliente` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre_cliente`, `apellido_cliente`, `dui_cliente`, `correo_cliente`, `telefono_cliente`, `direccion_cliente`, `nacimiento_cliente`, `clave_cliente`, `estado_cliente`, `fecha_registro`) VALUES
(1, 'fernando', 'moreno', '01758997-3', 'fa3528028@gmail.com', '7546-2612', 'fernando', '2020-12-01', '$2y$10$eIFCC.Zt15lGYQA85SgRjuftookqzZEsJ2s9vx7FRvJDpXTZgVasu', 1, '2024-05-23'),
(2, 'fernando', 'moreni', '46612021-0', 'fernandola017589978@gmail.com', '7541-2512', 'mi casa xd', '2005-02-20', '$2y$10$pNnP30hrCVbE1h96xpUsrep4jwwTOR8ba7.sl2jIGlOu9Zg5a0y86', 1, '2024-07-20'),
(3, 'alvaro', 'monterrosa', '59453623-0', 'alvarito@gmail.com', '7589-2612', 'mi casa', '2005-02-20', '$2y$10$O9hPgsBjSi8ZRTwIa7YvA.A/hroYLVFwLPfcpT7UMWTxIXzKeBz4a', 1, '2024-07-22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `id_producto` int(10) UNSIGNED NOT NULL,
  `cantidad_producto` smallint(6) UNSIGNED NOT NULL,
  `precio_producto` decimal(5,2) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id_detalle`, `id_producto`, `cantidad_producto`, `precio_producto`, `id_pedido`) VALUES
(5, 4, 1, 206.00, 1),
(6, 4, 1, 206.00, 2),
(8, 5, 1, 14.99, 3),
(9, 4, 1, 206.00, 4),
(10, 4, 1, 206.00, 5),
(11, 4, 1, 206.00, 6),
(12, 4, 1, 206.00, 7),
(13, 4, 1, 206.00, 8),
(14, 4, 1, 206.00, 9),
(15, 4, 1, 206.00, 10),
(16, 4, 1, 206.00, 11);

--
-- Disparadores `detalle_pedido`
--
DELIMITER $$
CREATE TRIGGER `calcular_subtotal_detalle_pedido` AFTER INSERT ON `detalle_pedido` FOR EACH ROW BEGIN
    DECLARE subtotal DECIMAL(10, 2);

    -- Calcular el subtotal (cantidad_producto * precio_producto)
    SET subtotal = NEW.cantidad_producto * NEW.precio_producto;

    -- Actualizar la tabla pedido con el nuevo subtotal
    UPDATE pedido
    SET subtotal_pedido = subtotal_pedido + subtotal
    WHERE id_pedido = NEW.id_pedido;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `direccion_pedido` varchar(250) NOT NULL,
  `estado_pedido` enum('Pendiente','Finalizado','Entregado','Anulado') NOT NULL,
  `fecha_registro` date NOT NULL DEFAULT current_timestamp(),
  `subtotal_pedido` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `id_cliente`, `direccion_pedido`, `estado_pedido`, `fecha_registro`, `subtotal_pedido`) VALUES
(1, 1, 'fernando', 'Finalizado', '2024-07-22', 0.00),
(2, 3, 'mi casa', 'Finalizado', '2024-07-22', 0.00),
(3, 1, 'fernando', 'Finalizado', '2024-07-23', 0.00),
(4, 1, 'fernando', 'Finalizado', '2024-07-23', 0.00),
(5, 1, 'fernando', 'Finalizado', '2024-07-24', 0.00),
(6, 1, 'fernando', 'Finalizado', '2024-07-24', 0.00),
(7, 1, 'fernando', 'Finalizado', '2024-07-24', 0.00),
(8, 1, 'fernando', 'Finalizado', '2024-07-24', 0.00),
(9, 1, 'fernando', 'Finalizado', '2024-07-24', 0.00),
(10, 1, 'fernando', 'Finalizado', '2024-07-24', 206.00),
(11, 1, 'fernando', 'Finalizado', '2024-07-24', 206.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(10) UNSIGNED NOT NULL,
  `nombre_producto` varchar(50) NOT NULL,
  `descripcion_producto` varchar(250) NOT NULL,
  `precio_producto` decimal(5,2) NOT NULL,
  `existencias_producto` int(10) UNSIGNED NOT NULL,
  `imagen_producto` varchar(25) NOT NULL,
  `id_categoria` int(10) UNSIGNED NOT NULL,
  `estado_producto` tinyint(1) NOT NULL,
  `id_administrador` int(10) UNSIGNED NOT NULL,
  `fecha_registro` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre_producto`, `descripcion_producto`, `precio_producto`, `existencias_producto`, `imagen_producto`, `id_categoria`, `estado_producto`, `id_administrador`, `fecha_registro`) VALUES
(1, 'la leyenda del ladron juan gomez jurado', 'Una aventura épica. Andalucía, 1587. En medio de un pueblo arrasado por la peste, uno de los comisarios de abastos del rey Felipe II encuentra a un niño que aún se aferra a la vida', 206.00, 0, '6661ee42daee8.jpg', 6, 0, 1, '2024-06-06'),
(4, 'la leyenda del ladron juan gomez', 'Una aventura épica. Andalucía, 1587. En medio de un pueblo arrasado por la peste, uno de los comisarios de abastos del rey Felipe II encuentra a un niño que aún se aferra a la vida', 206.00, 490, '66a089bce100e.jpg', 6, 1, 1, '2024-07-22'),
(5, 'Batman Año Uno', '. La historia muestra sus desafíos al enfrentarse a la corrupción de la ciudad, su relación con el comisionado James Gordon y su primer encuentro con algunos de los villanos más emblemáticos de Batman.', 14.99, 49, '66a0899b69ed7.jpg', 1, 1, 1, '2024-07-23'),
(6, 'prueba', 'prueba', 10.00, 2, '66a089cc052fe.jpg', 1, 1, 1, '2024-07-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `valoracion`
--

CREATE TABLE `valoracion` (
  `id_valoracion` int(11) NOT NULL,
  `id_producto` int(10) UNSIGNED DEFAULT NULL,
  `id_cliente` int(10) UNSIGNED DEFAULT NULL,
  `calificacion` decimal(3,1) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_valoracion` date NOT NULL,
  `estado_valoracion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `valoracion`
--

INSERT INTO `valoracion` (`id_valoracion`, `id_producto`, `id_cliente`, `calificacion`, `comentario`, `fecha_valoracion`, `estado_valoracion`) VALUES
(4, 4, 1, 5.0, 'me encanto la historia es fascinante', '2024-07-23', 1),
(5, 5, 1, 5.0, 'bueno', '2024-07-23', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_administrador`),
  ADD UNIQUE KEY `correo_usuario` (`correo_administrador`),
  ADD UNIQUE KEY `alias_usuario` (`alias_administrador`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `dui_cliente` (`dui_cliente`),
  ADD UNIQUE KEY `correo_cliente` (`correo_cliente`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `nombre_producto` (`nombre_producto`,`id_categoria`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_usuario` (`id_administrador`);

--
-- Indices de la tabla `valoracion`
--
ALTER TABLE `valoracion`
  ADD PRIMARY KEY (`id_valoracion`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_administrador` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `valoracion`
--
ALTER TABLE `valoracion`
  MODIFY `id_valoracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_administrador`) REFERENCES `administrador` (`id_administrador`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `valoracion`
--
ALTER TABLE `valoracion`
  ADD CONSTRAINT `valoracion_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON UPDATE CASCADE,
  ADD CONSTRAINT `valoracion_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
