-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2024 at 03:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booksadre`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertar_comentario` (IN `p_id_cliente` INT, IN `p_calificacion` INT, IN `p_comentario` VARCHAR(250), IN `p_id_producto` INT)   BEGIN

    DECLARE v_id_detalle INT;
 
    SELECT dp.id_detalle

    INTO v_id_detalle

    FROM detalle_pedido dp

    INNER JOIN pedido pe ON dp.id_pedido = pe.id_pedido

    WHERE pe.id_cliente = p_id_cliente

      AND dp.id_producto = p_id_producto

      AND (pe.estado_pedido = 'Entregado' OR pe.estado_pedido = 'Finalizado')

    ORDER BY dp.id_detalle DESC

    LIMIT 1;
 
    IF v_id_detalle IS NULL THEN

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Debe comprar el producto para poder comentar';

    ELSE

        INSERT INTO resena (id_detalle, calificacion_producto, comentario_producto, fecha_valoracion, estado_comentario)

        VALUES (v_id_detalle, p_calificacion, p_comentario, CURRENT_DATE, 1);

    END IF;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `administrador`
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
-- Dumping data for table `administrador`
--

INSERT INTO `administrador` (`id_administrador`, `nombre_administrador`, `apellido_administrador`, `correo_administrador`, `alias_administrador`, `clave_administrador`, `fecha_registro`) VALUES
(1, 'fernando', 'moreno', 'fa3528028@gmail.com', 'maycol', '$2y$10$F7CULg.rndbxLY7TGyzR0.kfEFH3iAniz.Pm1s4TgGEOkbZnyfWN6', '2024-05-23 08:18:19'),
(2, 'Adiel', 'Montepeque', 'adielmontepeque@gmail.com', 'AdielM', '$2y$10$RoeLJAf5yYKmxoiyTLHkxO0AkuSmoVgbN4KT8iNcpExhY8HRogqsW', '2024-06-07 08:01:03');

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(10) UNSIGNED NOT NULL,
  `nombre_categoria` varchar(50) NOT NULL,
  `descripcion_categoria` varchar(250) DEFAULT NULL,
  `imagen_categoria` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categoria`
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
-- Table structure for table `cliente`
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
-- Dumping data for table `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre_cliente`, `apellido_cliente`, `dui_cliente`, `correo_cliente`, `telefono_cliente`, `direccion_cliente`, `nacimiento_cliente`, `clave_cliente`, `estado_cliente`, `fecha_registro`) VALUES
(1, 'fernando', 'moreno', '01758997-3', 'fa3528028@gmail.com', '7546-2612', 'fernando', '2020-12-01', '$2y$10$eIFCC.Zt15lGYQA85SgRjuftookqzZEsJ2s9vx7FRvJDpXTZgVasu', 1, '2024-05-23'),
(2, 'Adiel', 'Montepeque', '11111111-1', 'adielmontepeque@gmail.com', '6823-7472', 'Mi casa', '2006-05-06', '$2y$10$H8NHcrnQQmUC/JZY6ErzLew8pKr0Rk25CF6D9UzKIjBzxkTRNxrmS', 1, '2024-06-07');

-- --------------------------------------------------------

--
-- Table structure for table `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle` int(10) UNSIGNED NOT NULL,
  `id_producto` int(10) UNSIGNED NOT NULL,
  `cantidad_producto` smallint(6) UNSIGNED NOT NULL,
  `precio_producto` decimal(5,2) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id_detalle`, `id_producto`, `cantidad_producto`, `precio_producto`, `id_pedido`) VALUES
(1, 1, 1, 206.00, 1),
(2, 1, 1, 206.00, 1),
(3, 1, 1, 206.00, 2),
(4, 1, 4, 824.00, 3),
(5, 2, 1, 100.00, 3),
(6, 1, 1, 206.00, 4);

-- --------------------------------------------------------

--
-- Table structure for table `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `direccion_pedido` varchar(250) NOT NULL,
  `estado_pedido` enum('Pendiente','Finalizado','Entregado','Anulado') NOT NULL,
  `fecha_registro` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pedido`
--

INSERT INTO `pedido` (`id_pedido`, `id_cliente`, `direccion_pedido`, `estado_pedido`, `fecha_registro`) VALUES
(1, 1, 'fernando', 'Finalizado', '2024-06-06'),
(2, 1, 'fernando', 'Pendiente', '2024-06-06'),
(3, 2, 'Mi casa', 'Finalizado', '2024-06-07'),
(4, 2, 'Mi casa', 'Finalizado', '2024-07-24');

-- --------------------------------------------------------

--
-- Table structure for table `producto`
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
-- Dumping data for table `producto`
--

INSERT INTO `producto` (`id_producto`, `nombre_producto`, `descripcion_producto`, `precio_producto`, `existencias_producto`, `imagen_producto`, `id_categoria`, `estado_producto`, `id_administrador`, `fecha_registro`) VALUES
(1, 'la leyenda del ladron juan gomez jurado', 'Una aventura épica. Andalucía, 1587. En medio de un pueblo arrasado por la peste, uno de los comisarios de abastos del rey Felipe II encuentra a un niño que aún se aferra a la vida', 206.00, 45, '6661ee42daee8.jpg', 6, 1, 1, '2024-06-06'),
(2, 'El libro troll', 'Es un libro', 100.00, 19, '66631395a58d1.jpg', 8, 1, 2, '2024-06-07');

-- --------------------------------------------------------

--
-- Table structure for table `valoracion`
--

CREATE TABLE `valoracion` (
  `id_valoracion` int(11) NOT NULL,
  `id_producto` int(11) UNSIGNED NOT NULL,
  `id_cliente` int(11) UNSIGNED NOT NULL,
  `calificacion` decimal(3,1) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_valoracion` date NOT NULL,
  `estado_valoracion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `valoracion`
--

INSERT INTO `valoracion` (`id_valoracion`, `id_producto`, `id_cliente`, `calificacion`, `comentario`, `fecha_valoracion`, `estado_valoracion`) VALUES
(3, 1, 2, 1.0, 'hola', '2024-07-24', 1),
(4, 1, 1, 5.0, 'hola', '2024-07-24', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id_administrador`),
  ADD UNIQUE KEY `correo_usuario` (`correo_administrador`),
  ADD UNIQUE KEY `alias_usuario` (`alias_administrador`);

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `dui_cliente` (`dui_cliente`),
  ADD UNIQUE KEY `correo_cliente` (`correo_cliente`);

--
-- Indexes for table `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indexes for table `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indexes for table `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `nombre_producto` (`nombre_producto`,`id_categoria`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_usuario` (`id_administrador`);

--
-- Indexes for table `valoracion`
--
ALTER TABLE `valoracion`
  ADD PRIMARY KEY (`id_valoracion`),
  ADD KEY `fk_producto_valoracion` (`id_producto`),
  ADD KEY `fk_cliente_valoracion` (`id_cliente`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id_administrador` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `valoracion`
--
ALTER TABLE `valoracion`
  MODIFY `id_valoracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON UPDATE CASCADE;

--
-- Constraints for table `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON UPDATE CASCADE;

--
-- Constraints for table `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_administrador`) REFERENCES `administrador` (`id_administrador`) ON UPDATE CASCADE;

--
-- Constraints for table `valoracion`
--
ALTER TABLE `valoracion`
  ADD CONSTRAINT `valoracion_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `valoracion_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
