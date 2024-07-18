-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-07-2024 a las 17:38:40
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_orden_validado` (IN `p_nueva_cantidad` INT, IN `p_id_detalle` INT, IN `p_id_cliente` INT)   BEGIN
    DECLARE p_cantidad_previa INT;
    DECLARE p_id_producto INT;
    DECLARE diferencia INT;
 
    -- Obtener la cantidad previa y el id_producto
    SELECT dp.cantidad_producto, dp.id_producto INTO p_cantidad_previa, p_id_producto
    FROM detalle_pedido dp
    JOIN pedido p ON dp.id_pedido = p.id_pedido
    WHERE dp.id_detalle = p_id_detalle
      AND p.id_cliente = p_id_cliente
      AND p.estado_pedido = 'Pendiente'
    LIMIT 1;
 
    -- Calcular la diferencia
    SET diferencia = p_cantidad_previa - p_nueva_cantidad;
 
    -- Actualizar la cantidad comprada en detalle_pedido
    UPDATE detalle_pedido
    SET cantidad_producto = p_nueva_cantidad
    WHERE id_detalle = p_id_detalle
      AND id_pedido = (SELECT id_pedido FROM pedido WHERE id_cliente = p_id_cliente AND estado_pedido = 'Pendiente' LIMIT 1);
 
    -- Ajustar las existencias en la tabla producto
    UPDATE producto
    SET existencias_producto = existencias_producto + diferencia
    WHERE id_producto = p_id_producto;
 
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `eliminar_orden_validado` (IN `p_id_detalle` INT, IN `p_id_cliente` INT)   BEGIN
    DECLARE p_cantidad_previa INT;
    DECLARE p_id_producto INT;
 
    -- Obtener la cantidad previa y el id_producto del detalle del pedido a eliminar
    SELECT dp.cantidad_producto, dp.id_producto INTO p_cantidad_previa, p_id_producto
    FROM detalle_pedido dp
    JOIN pedido p ON dp.id_pedido = p.id_pedido
    WHERE dp.id_detalle = p_id_detalle
      AND p.id_cliente = p_id_cliente
      AND p.estado_pedido = 'Pendiente'
    LIMIT 1;
 
    -- Ajustar las existencias en la tabla producto
    UPDATE producto
    SET existencias_producto = existencias_producto + p_cantidad_previa
    WHERE id_producto = p_id_producto;
 
    -- Eliminar el detalle del pedido
    DELETE FROM detalle_pedido
    WHERE id_detalle = p_id_detalle
      AND id_pedido = (SELECT id_pedido FROM pedido WHERE id_cliente = p_id_cliente AND estado_pedido = 'Pendiente' LIMIT 1);
    -- Mensaje de confirmación
    SELECT CONCAT('El detalle del pedido con ID ', p_id_detalle, ' ha sido eliminado y ', p_cantidad_previa, ' unidades han sido devueltas al inventario.') AS mensaje;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertar_orden_validado` (IN `p_id_cliente` INT, IN `p_cantidad_comprada` INT, IN `p_id_producto` INT)   BEGIN
    DECLARE p_precio_producto DECIMAL(10,2);
    DECLARE p_direccion_pedido VARCHAR(200);
    DECLARE p_id_pedido INT;
    DECLARE pedido_existente INT;
    DECLARE detalle_existente INT;
    DECLARE mensaje VARCHAR(255);
 
    -- Traer la dirección del cliente
    SELECT direccion_cliente INTO p_direccion_pedido 
    FROM cliente 
    WHERE id_cliente = p_id_cliente;
    -- Calcular el precio del producto multiplicado por la cantidad
    SELECT precio_producto INTO p_precio_producto 
    FROM producto 
    WHERE id_producto = p_id_producto;
    SET p_precio_producto = p_precio_producto * p_cantidad_comprada;
 
    -- Verificar si hay un pedido pendiente para el cliente
    SELECT id_pedido INTO pedido_existente 
    FROM pedido 
    WHERE id_cliente = p_id_cliente AND estado_pedido = 'Pendiente'
    LIMIT 1;
 
    IF pedido_existente IS NOT NULL THEN
        -- Si hay un pedido pendiente, usar ese ID de pedido
        SET p_id_pedido = pedido_existente;
    ELSE
        -- Si no hay un pedido pendiente, insertar un nuevo pedido
        INSERT INTO pedido (direccion_pedido, id_cliente)
        VALUES (p_direccion_pedido, p_id_cliente);
        -- Obtener el ID del nuevo pedido
        SET p_id_pedido = LAST_INSERT_ID();
    END IF;
 
    -- Verificar si el detalle del pedido ya existe para el mismo producto en el mismo pedido
    SELECT id_detalle INTO detalle_existente
    FROM detalle_pedido
    WHERE id_pedido = p_id_pedido AND id_producto = p_id_producto
    LIMIT 1;
 
    IF detalle_existente IS NOT NULL THEN
        -- Si ya existe, actualizar la cantidad y el precio
        UPDATE detalle_pedido 
        SET cantidad_producto = cantidad_producto + p_cantidad_comprada,
            precio_producto = precio_producto + p_precio_producto
        WHERE id_detalle = detalle_existente;
        SET mensaje = 'Producto actualizado en el carrito correctamente.';
    ELSE
        -- Si no existe, insertar el detalle del pedido
        INSERT INTO detalle_pedido (id_pedido, precio_producto, cantidad_producto, id_producto)
        VALUES (p_id_pedido, p_precio_producto, p_cantidad_comprada, p_id_producto);
        SET mensaje = 'Producto agregado al carrito correctamente.';
    END IF;
 
    -- Disminuir las existencias del producto en la tabla producto
    UPDATE producto
    SET existencias_producto = existencias_producto - p_cantidad_comprada
    WHERE id_producto = p_id_producto;
 
    SELECT mensaje;
END$$

DELIMITER ;

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
(6, 'Aventuras', 'narran historias increíbles de grandes personajes que han ido a la conquista de lo desconocido', '6661ec46d69b9.jpg'),
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
(1, 'fernando', 'moreno', '01758997-3', 'fa3528028@gmail.com', '7546-2612', 'fernando', '2020-12-01', '$2y$10$eIFCC.Zt15lGYQA85SgRjuftookqzZEsJ2s9vx7FRvJDpXTZgVasu', 1, '2024-05-23');

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
(1, 1, 1, 206.00, 1),
(2, 1, 1, 206.00, 1),
(3, 1, 39, 999.99, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `direccion_pedido` varchar(250) NOT NULL,
  `estado_pedido` enum('Pendiente','Finalizado','Entregado','Anulado') NOT NULL,
  `fecha_registro` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `id_cliente`, `direccion_pedido`, `estado_pedido`, `fecha_registro`) VALUES
(1, 1, 'fernando', 'Finalizado', '2024-06-06'),
(2, 1, 'fernando', 'Pendiente', '2024-06-06');

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
(1, 'la leyenda del ladron juan gomez jurado', 'Una aventura épica. Andalucía, 1587. En medio de un pueblo arrasado por la peste, uno de los comisarios de abastos del rey Felipe II encuentra a un niño que aún se aferra a la vida', 206.00, 12, '6661ee42daee8.jpg', 6, 1, 1, '2024-06-06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resena`
--

CREATE TABLE `resena` (
  `id_resena` int(10) UNSIGNED NOT NULL,
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `calificacion_producto` int(11) DEFAULT NULL,
  `comentario_producto` varchar(250) DEFAULT NULL,
  `fecha_valoracion` date NOT NULL,
  `estado_comentario` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indices de la tabla `resena`
--
ALTER TABLE `resena`
  ADD KEY `reseña_ibfk_1` (`id_detalle`);

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
  MODIFY `id_cliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Filtros para la tabla `resena`
--
ALTER TABLE `resena`
  ADD CONSTRAINT `reseña_ibfk_1` FOREIGN KEY (`id_detalle`) REFERENCES `detalle_pedido` (`id_detalle`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
